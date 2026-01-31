<?php
declare(strict_types=1);
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace stubbles\xml\serializer;

use ReflectionClass;
use ReflectionMethod;
use ReflectionObject;
use ReflectionProperty;
use stubbles\reflect\Attributes;
use stubbles\xml\serializer\attributes\XmlAttribute;
use stubbles\xml\serializer\attributes\XmlFragment;
use stubbles\xml\serializer\attributes\XmlIgnore;
use stubbles\xml\serializer\attributes\XmlTag;
use stubbles\xml\serializer\delegate\Attribute;
use stubbles\xml\serializer\delegate\Fragment;
use stubbles\xml\serializer\delegate\Tag;
use stubbles\xml\serializer\delegate\XmlSerializerDelegate;
use stubbles\xml\XmlStreamWriter;

use function stubbles\reflect\attributesOf;
use function stubbles\reflect\methodsOf;
use function stubbles\reflect\propertiesOf;

/**
 * Container for extracting informations on how to serialize a class via attributes.
 * 
 * @implements ObjectXmlSerializer<object>
 * @template T of object
 * @since 10.1
 */
class AttributeBasedObjectXmlSerializer implements ObjectXmlSerializer
{
    /** default tag name for class */
    private string $classTagName;
    /**
     * map of delegates to serialize properties of class with
     *
     * @var XmlSerializerDelegate[]
     */
    private array $properties = [];
    /**
     * map of delegates to serialize methods of class with
     *
     * @var XmlSerializerDelegate[]
     */
    private array $methods     = [];
    /**
     * map of serializer instances for different classes
     *
     * @var array<class-string<T>,AttributeBasedObjectXmlSerializer<T>>
     */
    private static array $cache = [];

    /**
     * constructor
     *
     * It is recommended to not use the constructor but the static fromObject()
     * method. The constructor should be used if one is sure that there is only
     * one instance of a class to serialize.
     *
     * @param ReflectionClass<T> $objectClass
     */
    public function __construct(ReflectionClass $objectClass)
    {
        $this->properties = $this->extractProperties($objectClass);
        $this->methods = $this->extractMethods($objectClass);
        $attributes = attributesOf($objectClass);
        if ($attributes->contain(XmlTag::class)) {
            $this->classTagName = $attributes->firstNamed(XmlTag::class)->tagName();
        } else {
            $this->classTagName = $objectClass->getShortName();
        }
    }

    /**
     * creates the structure from given object
     *
     * This method will cache the result - on the next request with the same
     * class it will return the same result, even if the given object is a
     * different instance.
     *
     * @param   T  $object
     * @return  AttributeBasedObjectXmlSerializer<T>
     */
    public static function fromObject(object $object): self
    {
        /** @var class-string<T> $className */
        $className = get_class($object);
        if (!isset(self::$cache[$className])) {
            /** @var ReflectionClass<T> */
            $ref = new ReflectionObject($object);
            self::$cache[$className] = new self($ref);
        }

        /** @var AttributeBasedObjectXmlSerializer<T> */
        return self::$cache[$className];
    }

    /**
     * serializes given value
     *
     * @param T $object
     */
    public function serialize(
        object $object,
        XmlSerializer $xmlSerializer,
        XmlStreamWriter $xmlWriter,
        ?string $tagName = null
    ): void {
        $xmlWriter->writeStartElement(null !== $tagName ? $tagName : $this->classTagName);
        foreach ($this->properties as $propertyName => $xmlSerializerDelegate) {
            $xmlSerializerDelegate->serialize(
                $object->$propertyName,
                $xmlSerializer,
                $xmlWriter
            );
        }

        foreach ($this->methods as $methodName => $xmlSerializerDelegate) {
            $xmlSerializerDelegate->serialize(
                $object->$methodName(),
                $xmlSerializer,
                $xmlWriter
            );
        }

        $xmlWriter->writeEndElement();
    }

    /**
     * @param  ReflectionClass<T>  $objectClass
     * @return XmlSerializerDelegate[]
     */
    private function extractProperties(ReflectionClass $objectClass): array
    {
        return propertiesOf($objectClass, ReflectionProperty::IS_PUBLIC)
            ->filter(fn(ReflectionProperty $property): bool => !$property->isStatic() && $property->getAttributes(XmlIgnore::class) !== null)
            ->map(fn(ReflectionProperty $property): XmlSerializerDelegate =>
                $this->createSerializerDelegate(
                    attributesOf($property),
                    $property->getName()
                )
            )->data();
    }

    /**
     * @param  ReflectionClass<T>  $objectClass
     * @return XmlSerializerDelegate[]
     */
    private function extractMethods(ReflectionClass $objectClass): array
    {
        return methodsOf($objectClass, ReflectionMethod::IS_PUBLIC)
            ->filter(
                function(ReflectionMethod $method): bool
                {
                    if (
                        $method->getNumberOfParameters() != 0
                        || $method->isStatic()
                        || $method->isConstructor()
                        || $method->isDestructor()
                        || 0 == strncmp($method->getName(), '__', 2))
                    {
                        return false;
                    }

                    return !attributesOf($method)->contain(XmlIgnore::class);
                }
            )->map(fn(ReflectionMethod $method): XmlSerializerDelegate =>
                $this->createSerializerDelegate(
                    attributesOf($method),
                    $method->getName()
                )
            )->data();
    }

    private function createSerializerDelegate(
        Attributes $attributes,
        string $defaultTagName
    ): XmlSerializerDelegate {
        if ($attributes->contain(XmlAttribute::class)) {
            return Attribute::createFromAttribute($attributes->firstNamed(XmlAttribute::class));
        } elseif ($attributes->contain(XmlFragment::class)) {
            return Fragment::createFromAttribute($attributes->firstNamed(XmlFragment::class));
        } elseif ($attributes->contain(XmlTag::class)) {
            return Tag::createFromAttribute($attributes->firstNamed(XmlTag::class));
        }

        return new Tag($defaultTagName);
    }
}
