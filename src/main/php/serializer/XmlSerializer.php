<?php
declare(strict_types=1);
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace stubbles\xml\serializer;
use stubbles\ioc\Injector;
use stubbles\xml\XmlStreamWriter;

use function stubbles\reflect\annotationsOf;
/**
 * Serializes arbitrary data except resources to xml.
 */
class XmlSerializer
{
    /**
     * injector to create object serializer instances
     *
     * @type  \stubbles\ioc\Injector
     */
    protected $injector;

    /**
     * constructor
     *
     * @param  \stubbles\ioc\Injector  $injector
     */
    public function  __construct(Injector $injector)
    {
        $this->injector = $injector;
    }

    /**
     * serialize any data structure to xml
     *
     * @param   mixed                          $value           data to serialize
     * @param   \stubbles\xml\XmlStreamWriter  $xmlWriter       xml writer to write serialized data into
     * @param   string                         $tagName         name of the surrounding xml tag
     * @param   string                         $elementTagName  recurring element tag name for lists
     * @return  \stubbles\xml\XmlStreamWriter
     */
    public function serialize(
            $value,
            XmlStreamWriter $xmlWriter,
            string $tagName = null,
            string $elementTagName = null
    ): XmlStreamWriter {
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
                if ($value instanceof \Traversable && !annotationsOf($value)->contain('XmlNonTraversable')) {
                    if (null === $tagName && $value instanceof \Traversable && annotationsOf($value)->contain('XmlTag')) {
                        $annotation = annotationsOf($value)->firstNamed('XmlTag');
                        $tagName = $annotation->getTagName();
                        $elementTagName = $annotation->getElementTagName();
                    }

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
     * @param   \stubbles\xml\XmlStreamWriter  $xmlWriter  xml writer to write serialized value into
     * @param   string                         $tagName    name of the surrounding xml tag
     * @return  \stubbles\xml\XmlStreamWriter
     * @since   1.6.0
     */
    public function serializeNull(XmlStreamWriter $xmlWriter, string $tagName = null): XmlStreamWriter
    {
        return $xmlWriter->writeStartElement(null === $tagName ? 'null' : $tagName)
                ->writeElement('null')
                ->writeEndElement();
    }

    /**
     * serializes boolean value to xml
     *
     * @param   bool                           $value
     * @param   \stubbles\xml\XmlStreamWriter  $xmlWriter  xml writer to write serialized value into
     * @param   string                         $tagName    name of the surrounding xml tag
     * @return  \stubbles\xml\XmlStreamWriter
     * @since   1.6.0
     */
    public function serializeBool($value, XmlStreamWriter $xmlWriter, string $tagName = null): XmlStreamWriter
    {
        return $this->serializeScalarValue(
                $this->convertBoolToString($value),
                $xmlWriter,
                null === $tagName ? 'boolean' : $tagName
        );
    }

    /**
     * converts a boolean value into a useable xml string
     *
     * @param   bool  $value
     * @return  string
     * @since   2.0.0
     */
    public function convertBoolToString(bool $value): string
    {
        if (true === $value) {
            return 'true';
        }

        return 'false';
    }

    /**
     * serializes string to xml
     *
     * @param   string                         $value
     * @param   \stubbles\xml\XmlStreamWriter  $xmlWriter  xml writer to write serialized value into
     * @param   string                         $tagName    name of the surrounding xml tag
     * @return  \stubbles\xml\XmlStreamWriter
     * @since   1.6.0
     */
    public function serializeString($value, XmlStreamWriter $xmlWriter, string $tagName = null): XmlStreamWriter
    {
        return $this->serializeScalarValue($value, $xmlWriter, $tagName);
    }

    /**
     * serializes integer to xml
     *
     * @param   int                            $value
     * @param   \stubbles\xml\XmlStreamWriter  $xmlWriter  xml writer to write serialized value into
     * @param   string                         $tagName    name of the surrounding xml tag
     * @return  \stubbles\xml\XmlStreamWriter
     * @since   1.6.0
     */
    public function serializeInt($value, XmlStreamWriter $xmlWriter, string $tagName = null): XmlStreamWriter
    {
        return $this->serializeScalarValue($value, $xmlWriter, $tagName);
    }

    /**
     * serializes float value to xml
     *
     * @param   float                          $value
     * @param   \stubbles\xml\XmlStreamWriter  $xmlWriter  xml writer to write serialized value into
     * @param   string                         $tagName    name of the surrounding xml tag
     * @return  \stubbles\xml\XmlStreamWriter
     * @since   1.6.0
     */
    public function serializeFloat($value, XmlStreamWriter $xmlWriter, string $tagName = null): XmlStreamWriter
    {
        return $this->serializeScalarValue($value, $xmlWriter, $tagName);
    }

    /**
     * serializes any scalar value to xml
     *
     * @param   scalar                         $value
     * @param   \stubbles\xml\XmlStreamWriter  $xmlWriter  xml writer to write serialized value into
     * @param   string                         $tagName    name of the surrounding xml tag
     * @return  \stubbles\xml\XmlStreamWriter
     */
    protected function serializeScalarValue($value, XmlStreamWriter $xmlWriter, string $tagName = null): XmlStreamWriter
    {
        return $xmlWriter->writeStartElement(null === $tagName ? gettype($value) : $tagName)
                ->writeText(strval($value))
                ->writeEndElement();
    }

    /**
     * serializes an array to xml
     *
     * @param   array                          $array           array to serialize
     * @param   \stubbles\xml\XmlStreamWriter  $xmlWriter       xml writer to write serialized array into
     * @param   string                         $tagName         name of the surrounding xml tag
     * @param   string                         $elementTagName  necurring element tag name for lists
     * @return  \stubbles\xml\XmlStreamWriter
     * @since   1.6.0
     */
    public function serializeArray(
            $array,
            XmlStreamWriter $xmlWriter,
            string $tagName = null,
            string $elementTagName = null
    ): XmlStreamWriter {
        if (null === $tagName) {
            $tagName = 'array';
        }

        if (!empty($tagName)) {
            $xmlWriter->writeStartElement($tagName);
        }

        foreach ($array as $key => $value) {
            if (is_int($key)) {
                $this->serialize($value, $xmlWriter, $elementTagName);
            } else {
                $this->serialize($value, $xmlWriter, $key);
            }
        }

        if (!empty($tagName)) {
            $xmlWriter->writeEndElement();
        }

        return $xmlWriter;
    }

    /**
     * serializes an object to xml
     *
     * @param   object                         $object     object to serialize
     * @param   \stubbles\xml\XmlStreamWriter  $xmlWriter  xml writer to write serialized object into
     * @param   string                         $tagName    name of the surrounding xml tag
     * @return  \stubbles\xml\XmlStreamWriter
     * @since   1.6.0
      */
    public function serializeObject($object, XmlStreamWriter $xmlWriter, string $tagName = null): XmlStreamWriter
    {
        $this->serializerFor($object)->serialize($object, $this, $xmlWriter, $tagName);
        return $xmlWriter;
    }

    /**
     * returns serializer for given object
     *
     * @param   object  $object
     * @return  \stubbles\xml\serializer\ObjectXmlSerializer
     */
    protected function serializerFor($object): ObjectXmlSerializer
    {
        if (!annotationsOf($object)->contain('XmlSerializer')) {
            return AnnotationBasedObjectXmlSerializer::fromObject($object);
        }

        return $this->injector->getInstance(
                annotationsOf($object)
                        ->firstNamed('XmlSerializer')
                        ->getValue()
                        ->getName()
        );
    }
}
