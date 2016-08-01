<?php
declare(strict_types=1);
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  stubbles\xml
 */
namespace stubbles\xml\serializer;
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
 */
class AnnotationBasedObjectXmlSerializer implements ObjectXmlSerializer
{
    /**
     * default tag name for class
     *
     * @type  string
     */
    private $classTagName;
    /**
     * map of delegates to serialize properties of class with
     *
     * @type  \stubbles\xml\serializer\delegate\XmlSerializerDelegate[]
     */
    private $properties  = [];
    /**
     * map of delegates to serialize methods of class with
     *
     * @type  \stubbles\xml\serializer\delegate\XmlSerializerDelegate[]
     */
    private $methods     = [];
    /**
     * map of serializer instances for different classes
     *
     * @type  \stubbles\xml\serializer\AnnotationBasedObjectXmlSerializer[]
     */
    private static $cache = [];

    /**
     * constructor
     *
     * It is recommended to not use the constructor but the static fromObject()
     * method. The constructor should be used if one is sure that there is only
     * one instance of a class to serialize.
     *
     * @param  \ReflectionClass  $objectClass
     */
    public function __construct(\ReflectionClass $objectClass)
    {
        $this->extractProperties($objectClass);
        $this->extractMethods($objectClass);
        $annotations = annotationsOf($objectClass);
        if ($annotations->contain('XmlTag')) {
            $this->classTagName = $annotations->firstNamed('XmlTag')->tagName();
        } else {
            $className = $objectClass->getName();
            $this->classTagName = substr($className, strrpos($className, '\\') + 1);
        }
    }

    /**
     * creates the structure from given object
     *
     * This method will cache the result - on the next request with the same
     * class it will return the same result, even if the given object is a
     * different instance.
     *
     * @param   object  $object
     * @return  \stubbles\xml\serializer\AnnotationBasedObjectXmlSerializer
     */
    public static function fromObject($object): self
    {
        $className = get_class($object);
        if (isset(self::$cache[$className])) {
            return self::$cache[$className];
        }

        self::$cache[$className] = new self(new \ReflectionObject($object));
        return self::$cache[$className];
    }

    /**
     * serializes given value
     *
     * @param  mixed                                   $object
     * @param  \stubbles\xml\serializer\XmlSerializer  $xmlSerializer  serializer in case $value is not just a scalar value
     * @param  \stubbles\xml\XmlStreamWriter           $xmlWriter      xml writer to write serialized object into
     * @param  string                                  $tagName        name of the surrounding xml tag
     */
    public function serialize($object, XmlSerializer $xmlSerializer, XmlStreamWriter $xmlWriter, string $tagName = null)
    {
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
     * @param  \ReflectionClass  $objectClass
     */
    private function extractProperties(\ReflectionClass $objectClass)
    {
        $properties = propertiesOf($objectClass, \ReflectionProperty::IS_PUBLIC)
                ->filter(
                        function(\ReflectionProperty $property)
                        {
                            return !$property->isStatic() && !annotationsOf($property)->contain('XmlIgnore');
                        }
        );
        foreach ($properties as $property) {
            $this->properties[$property->getName()] = $this->createSerializerDelegate(
                    annotationsOf($property),
                    $property->getName()
            );
        }
    }

    /**
     * extract informations about methods
     *
     * @param  \ReflectionClass  $objectClass
     */
    private function extractMethods(\ReflectionClass $objectClass)
    {
        $methods = methodsOf($objectClass, \ReflectionMethod::IS_PUBLIC)
                ->filter(
                        function(\ReflectionMethod $method)
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
        );
        foreach ($methods as $method) {
            $this->methods[$method->getName()] = $this->createSerializerDelegate(
                    annotationsOf($method),
                    $method->getName()
            );
        }
    }

    /**
     * extracts informations about annotated element
     *
     * @param   \stubbles\lang\reflect\annotation\Annotations  $annotations     annotations of the element to serialize
     * @param   string                                         $defaultTagName  default tag name in case element is not annotated
     * @return  \stubbles\xml\serializer\delegate\XmlSerializerDelegate
     */
    private function createSerializerDelegate(Annotations $annotations, string $defaultTagName): XmlSerializerDelegate
    {
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
