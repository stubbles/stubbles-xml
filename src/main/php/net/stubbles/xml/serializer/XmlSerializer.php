<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  net\stubbles\xml
 */
namespace net\stubbles\xml\serializer;
use net\stubbles\ioc\Injector;
use net\stubbles\lang\reflect\ReflectionObject;
use net\stubbles\xml\XmlStreamWriter;
/**
 * Serializes arbitrary data except resources to xml.
 */
class XmlSerializer
{
    /**
     * injector to create object serializer instances
     *
     * @type  Injector
     */
    protected $injector;

    /**
     * constructor
     *
     * @param  Injector  $injector
     * @Inject
     */
    public function  __construct(Injector $injector)
    {
        $this->injector = $injector;
    }

    /**
     * serialize any data structure to xml
     *
     * @param   mixed            $value           data to serialize
     * @param   XmlStreamWriter  $xmlWriter       xml writer to write serialized data into
     * @param   string           $tagName         name of the surrounding xml tag
     * @param   string           $elementTagName  recurring element tag name for lists
     * @return  XmlStreamWriter
     */
    public function serialize($value, XmlStreamWriter $xmlWriter, $tagName = null, $elementTagName = null)
    {
        switch (gettype($value)) {
            case 'NULL':
                $this->serializeNull($xmlWriter, $tagName);
                break;

            case 'boolean':
                $this->serializeBool($value, $xmlWriter, $tagName);
                break;

            case 'string':
            case 'integer':
            case 'double':
                $this->serializeScalarValue($value, $xmlWriter, $tagName);
                break;

            case 'array':
                $this->serializeArray($value, $xmlWriter, $tagName, $elementTagName);
                break;

            case 'object':
                if ($value instanceof \Iterator) {
                    $this->serializeArray($value, $xmlWriter, $tagName, $elementTagName);
                } else {
                    $this->serializeObject($value, $xmlWriter, $tagName);
                }
                break;

            default:
                // nothing to do
        }

        return $xmlWriter;
    }

    /**
     * serializes null to xml
     *
     * @param   XmlStreamWriter  $xmlWriter  xml writer to write serialized value into
     * @param   string           $tagName    name of the surrounding xml tag
     * @return  XmlStreamWriter
     * @since   1.6.0
     */
    public function serializeNull(XmlStreamWriter $xmlWriter, $tagName = null)
    {
        if (null === $tagName) {
            $tagName = 'null';
        }

        $xmlWriter->writeStartElement($tagName);
        $xmlWriter->writeElement('null');
        $xmlWriter->writeEndElement();
        return $xmlWriter;
    }

    /**
     * serializes boolean value to xml
     *
     * @param   bool             $value
     * @param   XmlStreamWriter  $xmlWriter  xml writer to write serialized value into
     * @param   string           $tagName    name of the surrounding xml tag
     * @return  XmltreamWriter
     * @since   1.6.0
     */
    public function serializeBool($value, XmlStreamWriter $xmlWriter, $tagName = null)
    {
        if (null === $tagName) {
            $tagName = 'boolean';
        }

        return $this->serializeScalarValue($this->convertBoolToString($value), $xmlWriter, $tagName);
    }

    /**
     * converts a boolean value into a useable xml string
     *
     * @param   bool  $value
     * @return  string
     * @since   2.0.0
     */
    public function convertBoolToString($value)
    {
        if (true === $value) {
            return 'true';
        }

        return 'false';
    }

    /**
     * serializes string to xml
     *
     * @param   string           $value
     * @param   XmlStreamWriter  $xmlWriter  xml writer to write serialized value into
     * @param   string           $tagName    name of the surrounding xml tag
     * @return  XmlStreamWriter
     * @since   1.6.0
     */
    public function serializeString($value, XmlStreamWriter $xmlWriter, $tagName = null)
    {
        return $this->serializeScalarValue($value, $xmlWriter, $tagName);
    }

    /**
     * serializes integer to xml
     *
     * @param   int              $value
     * @param   XmlStreamWriter  $xmlWriter  xml writer to write serialized value into
     * @param   string           $tagName    name of the surrounding xml tag
     * @return  XmlStreamWriter
     * @since   1.6.0
     */
    public function serializeInt($value, XmlStreamWriter $xmlWriter, $tagName = null)
    {
        return $this->serializeScalarValue($value, $xmlWriter, $tagName);
    }

    /**
     * serializes float value to xml
     *
     * @param   float            $value
     * @param   XmlStreamWriter  $xmlWriter  xml writer to write serialized value into
     * @param   string           $tagName    name of the surrounding xml tag
     * @return  XmlStreamWriter
     * @since   1.6.0
     */
    public function serializeFloat($value, XmlStreamWriter $xmlWriter, $tagName = null)
    {
        return $this->serializeScalarValue($value, $xmlWriter, $tagName);
    }

    /**
     * serializes any scalar value to xml
     *
     * @param   scalar           $value
     * @param   XmlStreamWriter  $xmlWriter  xml writer to write serialized value into
     * @param   string           $tagName    name of the surrounding xml tag
     * @return  XmlStreamWriter
     */
    protected function serializeScalarValue($value, XmlStreamWriter $xmlWriter, $tagName = null)
    {
        if (null === $tagName) {
            $tagName = gettype($value);
        }

        $xmlWriter->writeStartElement($tagName);
        $xmlWriter->writeText(strval($value));
        $xmlWriter->writeEndElement();
        return $xmlWriter;
    }

    /**
     * serializes an array to xml
     *
     * @param   array            $array           array to serialize
     * @param   XmlStreamWriter  $xmlWriter       xml writer to write serialized array into
     * @param   string           $tagName         name of the surrounding xml tag
     * @param   string           $elementTagName  necurring element tag name for lists
     * @return  XmlStreamWriter
     * @since   1.6.0
     */
    public function serializeArray($array, XmlStreamWriter $xmlWriter, $tagName = null, $elementTagName = null)
    {
        if (null === $tagName) {
            $tagName = 'array';
        }

        if (false !== $tagName) {
            $xmlWriter->writeStartElement($tagName);
        }

        foreach ($array as $key => $value) {
            if (is_int($key)) {
                $this->serialize($value, $xmlWriter, $elementTagName);
            } else {
                $this->serialize($value, $xmlWriter, $key);
            }
        }

        if (false !== $tagName) {
            $xmlWriter->writeEndElement();
        }

        return $xmlWriter;
    }

    /**
     * serializes an object to xml
     *
     * @param   object           $object     object to serialize
     * @param   XmlStreamWriter  $xmlWriter  xml writer to write serialized object into
     * @param   string           $tagName    name of the surrounding xml tag
     * @return  XmlStreamWriter
     * @since   1.6.0
      */
    public function serializeObject($object, XmlStreamWriter $xmlWriter, $tagName = null)
    {
        $this->getObjectSerializer($object)->serialize($object, $this, $xmlWriter, $tagName);
        return $xmlWriter;
    }

    /**
     * returns serializer for given object
     *
     * @param   object  $object
     * @return  XmlObjectSerializer
     */
    protected function getObjectSerializer($object)
    {
        $objectClass = new ReflectionObject($object);
        if (!$objectClass->hasAnnotation('XmlSerializer')) {
            return AnnotationBasedObjectXmlSerializer::forClass($objectClass);
        }

        return $this->injector->getInstance($objectClass->getAnnotation('XmlSerializer')
                                                        ->getSerializerClass()
                                                        ->getName()
               );
    }
}
?>