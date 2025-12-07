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
use Traversable;

use function stubbles\reflect\annotationsOf;
/**
 * Serializes arbitrary data except resources to xml.
 */
class XmlSerializer
{
    public function  __construct(protected Injector $injector) { }

    /**
     * serialize any data structure to xml
     */
    public function serialize(
        mixed $value,
        XmlStreamWriter $xmlWriter,
        ?string $tagName = null,
        ?string $elementTagName = null
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
                if (
                    $value instanceof Traversable
                    && !annotationsOf($value)->contain('XmlNonTraversable')
                ) {
                    if (
                        null === $tagName
                        && $value instanceof Traversable
                        && annotationsOf($value)->contain('XmlTag')
                    ) {
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
     * @since   1.6.0
     */
    public function serializeNull(
        XmlStreamWriter $xmlWriter,
        ?string $tagName = null
    ): XmlStreamWriter {
        return $xmlWriter->writeStartElement(null === $tagName ? 'null' : $tagName)
            ->writeElement('null')
            ->writeEndElement();
    }

    /**
     * serializes boolean value to xml
     *
     * @since   1.6.0
     */
    public function serializeBool(
        bool $value,
        XmlStreamWriter $xmlWriter,
        ?string $tagName = null
    ): XmlStreamWriter {
        return $this->serializeScalarValue(
            $this->convertBoolToString($value),
            $xmlWriter,
            $tagName ?? 'boolean'
        );
    }

    /**
     * converts a boolean value into a useable xml string
     *
     * @since   2.0.0
     */
    public function convertBoolToString(bool $value): string
    {
        return $value ? 'true' : 'false';
    }

    /**
     * serializes string to xml
     *
     * @since   1.6.0
     */
    public function serializeString(
        string $value,
        XmlStreamWriter $xmlWriter,
        ?string $tagName = null
    ): XmlStreamWriter {
        return $this->serializeScalarValue($value, $xmlWriter, $tagName);
    }

    /**
     * serializes integer to xml
     *
     * @since   1.6.0
     */
    public function serializeInt(
        int $value,
        XmlStreamWriter $xmlWriter,
        ?string $tagName = null
    ): XmlStreamWriter {
        return $this->serializeScalarValue($value, $xmlWriter, $tagName);
    }

    /**
     * serializes float value to xml
     *
     * @since   1.6.0
     */
    public function serializeFloat(
        float $value,
        XmlStreamWriter $xmlWriter,
        ?string $tagName = null
    ): XmlStreamWriter {
        return $this->serializeScalarValue($value, $xmlWriter, $tagName);
    }

    /**
     * serializes any scalar value to xml
     *
     * @param   scalar  $value
     */
    protected function serializeScalarValue(
        $value,
        XmlStreamWriter $xmlWriter,
        ?string $tagName = null
    ): XmlStreamWriter {
        return $xmlWriter->writeStartElement($tagName ?? gettype($value))
            ->writeText(strval($value))
            ->writeEndElement();
    }

    /**
     * serializes an array to xml
     *
     * @param   iterable<mixed>  $array  array to serialize
     * @since   1.6.0
     */
    public function serializeArray(
        iterable $array,
        XmlStreamWriter $xmlWriter,
        ?string $tagName = null,
        ?string $elementTagName = null
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
     * @since   1.6.0
      */
    public function serializeObject(
        object $object,
        XmlStreamWriter $xmlWriter,
        ?string $tagName = null
    ): XmlStreamWriter {
        $this->serializerFor($object)->serialize($object, $this, $xmlWriter, $tagName);
        return $xmlWriter;
    }

    /**
     * returns serializer for given object
     *
     * @template T of object
     * @param   T  $object
     * @return  ObjectXmlSerializer<T>
     */
    protected function serializerFor(object $object): ObjectXmlSerializer
    {
        if (!annotationsOf($object)->contain('XmlSerializer')) {
            /** @var ObjectXmlSerializer<T> */
            return AnnotationBasedObjectXmlSerializer::fromObject($object);
        }

        /** @var  ObjectXmlSerializer<T> */
        return $this->injector->getInstance(
            annotationsOf($object)
                ->firstNamed('XmlSerializer')
                ->getValue()
                ->getName()
        );
    }
}
