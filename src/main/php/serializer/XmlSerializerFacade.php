<?php
declare(strict_types=1);
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace stubbles\xml\serializer;
use stubbles\xml\XmlStreamWriter;
/**
 * Facade to simplify xml serializing.
 *
 * @since  1.1.0
 */
class XmlSerializerFacade
{
    /**
     * xml serializer to hide
     *
     * @var  \stubbles\xml\serializer\XmlSerializer
     */
    private $xmlSerializer;
    /**
     * xml stream writer to write serialization to
     *
     * @var  \stubbles\xml\XmlStreamWriter
     */
    private $xmlStreamWriter;

    /**
     * constructor
     *
     * @param  \stubbles\xml\serializer\XmlSerializer  $xmlSerializer
     * @param  \stubbles\xml\XmlStreamWriter           $xmlStreamWriter
     */
    public function __construct(XmlSerializer $xmlSerializer, XmlStreamWriter $xmlStreamWriter)
    {
        $this->xmlSerializer   = $xmlSerializer;
        $this->xmlStreamWriter = $xmlStreamWriter;
    }

    /**
     * serialize any data structure to XML
     *
     * @param   mixed   $data     data to serialize
     * @param   string  $tagName  name for root tag
     * @return  string
     */
    public function serializeToXml($data, string $tagName = null): string
    {
        return $this->xmlSerializer->serialize($data, $this->xmlStreamWriter, $tagName)
               ->asXml();
    }

    /**
     * serialize any data structure to XML
     *
     * @param   mixed   $data     data to serialize
     * @param   string  $tagName  name for root tag
     * @return  \DOMDocument
     */
    public function serializeToDom($data, string $tagName = null): \DOMDocument
    {
        return $this->xmlSerializer->serialize($data, $this->xmlStreamWriter, $tagName)
               ->asDOM();
    }
}
