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
use stubbles\reflect\annotation\Annotations;
use stubbles\xml\XmlStreamWriter;
use stubbles\xml\serializer\delegate\{
    Attribute,
    Fragment,
    Tag,
    XmlSerializerDelegate
};

use function stubbles\reflect\annotationsOf;
use function stubbles\reflect\methodsOf;
use function stubbles\reflect\propertiesOf;
/**
 * Container for extracting informations on how to serialize a class.
 * 
 * @implements ObjectXmlSerializer<object>
 * @template T of object
 */
class AnnotationBasedObjectXmlSerializer implements ObjectXmlSerializer
{
    /** default tag name for class */
    private string $classTagName;
    /**
     * map of delegates to serialize properties of class with
     *
     * @var  XmlSerializerDelegate[]
     */
    private array $properties = [];
    /**
     * map of delegates to serialize methods of class with
     *
     * @var  XmlSerializerDelegate[]
     */
    private array $methods     = [];
    /**
     * map of serializer instances for different classes
     *
     * @var  array<class-string<T>,AnnotationBasedObjectXmlSerializer<T>>
     */
    private static array $cache = [];

    /**
     * constructor
     *
     * It is recommended to not use the constructor but the static fromObject()
     * method. The constructor should be used if one is sure that there is only
     * one instance of a class to serialize.
     *
     * @param  ReflectionClass<T>  $objectClass
     */
    public function __construct(ReflectionClass $objectClass)
    {
        $this->properties = $this->extractProperties($objectClass);
        $this->methods    = $this->extractMethods($objectClass);
        $annotations      = annotationsOf($objectClass);
        if ($annotations->contain('XmlTag')) {
            $this->classTagName = $annotations->firstNamed('XmlTag')->tagName();
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
     * @return  AnnotationBasedObjectXmlSerializer<T>
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

        /** @var AnnotationBasedObjectXmlSerializer<T> */
        return self::$cache[$className];
    }

    /**
     * serializes given value
     *
     * @param  T  $object
     */
    public function serialize(
            object $object,
            XmlSerializer $xmlSerializer,
            XmlStreamWriter $xmlWriter,
            string $tagName = null
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
     * extract informations about properties
     *
     * @param   \ReflectionClass<T>  $objectClass
     * @return  XmlSerializerDelegate[]
     */
    private function extractProperties(\ReflectionClass $objectClass): array
    {
        return propertiesOf($objectClass, \ReflectionProperty::IS_PUBLIC)
                ->filter(function(\ReflectionProperty $property)
                        {
                            return !$property->isStatic()
                                && !annotationsOf($property)->contain('XmlIgnore');
                        }
                )->map(function(\ReflectionProperty $property)
                        {
                            return $this->createSerializerDelegate(
                                    annotationsOf($property),
                                    $property->getName()
                            );
                        }
                )->data();
    }

    /**
     * extract informations about methods
     *
     * @param   ReflectionClass<T>  $objectClass
     * @return  XmlSerializerDelegate[]
     */
    private function extractMethods(ReflectionClass $objectClass): array
    {
        return methodsOf($objectClass, ReflectionMethod::IS_PUBLIC)
            ->filter(
                function(ReflectionMethod $method): bool
                {
                    if ($method->getNumberOfParameters() != 0
                            || $method->isStatic()
                            || $method->isConstructor()
                            || $method->isDestructor()
                            || 0 == strncmp($method->getName(), '__', 2)) {
                        return false;
                    }

                    return !annotationsOf($method)->contain('XmlIgnore');
                }
            )->map(
                function(ReflectionMethod $method): XmlSerializerDelegate
                {
                    return $this->createSerializerDelegate(
                        annotationsOf($method),
                        $method->getName()
                    );
                }

            )->data();
    }

    /**
     * extracts informations about annotated element
     */
    private function createSerializerDelegate(
            Annotations $annotations,
            string $defaultTagName
    ): XmlSerializerDelegate {
        if ($annotations->contain('XmlAttribute')) {
            $xmlAttribute = $annotations->firstNamed('XmlAttribute');
            return new Attribute(
                $xmlAttribute->attributeName(),
                $xmlAttribute->getValueByName('skipEmpty', true)
            );
        } elseif ($annotations->contain('XmlFragment')) {
            $xmlFragment = $annotations->firstNamed('XmlFragment');
            return new Fragment(
                false !== $xmlFragment->tagName() ? $xmlFragment->tagName() : null,
                $xmlFragment->getValueByName('transformNewLineToBr', false)
            );
        } elseif ($annotations->contain('XmlTag')) {
            $xmlTag = $annotations->firstNamed('XmlTag');
            return new Tag(
                false !== $xmlTag->tagName() ? $xmlTag->tagName() : '',
                $xmlTag->getValueByName('elementTagName')
            );
        }

        return new Tag($defaultTagName);
    }
}
