<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  stubbles\xml
 */
namespace stubbles\xml\serializer;
use stubbles\lang\reflect\BaseReflectionClass;
use stubbles\lang\reflect\annotation\Annotatable;
use stubbles\xml\XmlStreamWriter;
use stubbles\xml\serializer\delegate\XmlSerializerAttributeDelegate;
use stubbles\xml\serializer\delegate\XmlSerializerFragmentDelegate;
use stubbles\xml\serializer\delegate\XmlSerializerTagDelegate;
use stubbles\xml\serializer\matcher\XmlSerializerMethodPropertyMatcher;
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
    protected $classTagName;
    /**
     * list of properties to serialize
     *
     * @type  array
     */
    protected $properties  = [];
    /**
     * list of methods to serialize
     *
     * @type  array
     */
    protected $methods     = [];
    /**
     * reflection instance of class to serialize
     *
     * @type  \stubbles\lang\reflect\BaseReflectionClass
     */
    protected $refClass;
    /**
     * the matcher to be used for methods and properties
     *
     * @type  \stubbles\xml\serializer\matcher\XmlSerializerMethodPropertyMatcher
     */
    protected static $methodAndPropertyMatcher;
    /**
     * simple cache
     *
     * @type  array
     */
    protected static $cache = [];

    /**
     * static initializer
     */
    public static function __static()
    {
        self::$methodAndPropertyMatcher = new XmlSerializerMethodPropertyMatcher();
    }

    /**
     * constructor
     *
     * It is recommended to not use the constructor but the static fromObject()
     * method. The constructor should be used if one is sure that there is only
     * one instance of a class to serialize.
     *
     * @param  \stubbles\lang\reflect\BaseReflectionClass  $objectClass
     */
    public function __construct(BaseReflectionClass $objectClass)
    {
        $this->refClass = $objectClass;
        $this->extractProperties();
        $this->extractMethods();
    }

    /**
     * creates the structure from given object
     *
     * This method will cache the result - on the next request with the same
     * class it will return the same result, even if the given object is a
     * different instance.
     *
     * @param   \stubbles\lang\reflect\BaseReflectionClass  $objectClass
     * @return  \stubbles\xml\serializer\AnnotationBasedObjectXmlSerializer
     */
    public static function forClass(BaseReflectionClass $objectClass)
    {
        $className = $objectClass->getName();
        if (isset(self::$cache[$className])) {
            return self::$cache[$className];
        }

        self::$cache[$className] = new self($objectClass);
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
    public function serialize($object, XmlSerializer $xmlSerializer, XmlStreamWriter $xmlWriter, $tagName)
    {
        $xmlWriter->writeStartElement($this->getClassTagName($tagName));
        foreach ($this->properties as $propertyName => $xmlSerializerDelegate) {
            $xmlSerializerDelegate->serialize($object->$propertyName, $xmlSerializer, $xmlWriter);
        }

        foreach ($this->methods as $methodName => $xmlSerializerDelegate) {
            $xmlSerializerDelegate->serialize($object->$methodName(), $xmlSerializer, $xmlWriter);
        }

        $xmlWriter->writeEndElement();
    }

    /**
     * returns tag name for the class itself
     *
     * @param   string  $tagName  default tag name to be used
     * @return  string
     */
    protected function getClassTagName($tagName)
    {
        if (null !== $tagName) {
            return $tagName;
        }

        if ($this->refClass->hasAnnotation('XmlTag')) {
            return $this->refClass->getAnnotation('XmlTag')->getTagName();
        }

        $className = $this->refClass->getName();
        return substr($className, strrpos($className, '\\') + 1);
    }

    /**
     * extract informations about properties
     */
    protected function extractProperties()
    {
        foreach ($this->refClass->getPropertiesByMatcher(self::$methodAndPropertyMatcher) as $property) {
            $this->properties[$property->getName()] = $this->createSerializerDelegate($property, $property->getName());
        }
    }

    /**
     * extract informations about methods
     */
    protected function extractMethods()
    {
        foreach ($this->refClass->getMethodsByMatcher(self::$methodAndPropertyMatcher) as $method) {
            $this->methods[$method->getName()] = $this->createSerializerDelegate($method, $method->getName());
        }
    }

    /**
     * extracts informations about annotated element
     *
     * @param   \stubbles\lang\reflect\annotation\Annotatable  $annotatable      the annotatable element to serialize
     * @param   string                                         $annotatableName  name of annotatable element
     * @return  \stubbles\xml\serializer\delegate\XmlSerializerDelegate
     */
    protected function createSerializerDelegate(Annotatable $annotatable, $annotatableName)
    {
        if ($annotatable->hasAnnotation('XmlAttribute')) {
            $xmlAttribute = $annotatable->getAnnotation('XmlAttribute');
            return new XmlSerializerAttributeDelegate($xmlAttribute->getAttributeName(), $xmlAttribute->getSkipEmpty(true));
        } elseif ($annotatable->hasAnnotation('XmlFragment')) {
            $xmlFragment = $annotatable->getAnnotation('XmlFragment');
            return new XmlSerializerFragmentDelegate($xmlFragment->getTagName(), $xmlFragment->isTransformNewLineToBr());
        } elseif ($annotatable->hasAnnotation('XmlTag')) {
            $xmlTag = $annotatable->getAnnotation('XmlTag');
            return new XmlSerializerTagDelegate($xmlTag->getTagName(), $xmlTag->getElementTagName());
        }

        return new XmlSerializerTagDelegate($annotatableName);
    }
}
AnnotationBasedObjectXmlSerializer::__static();
