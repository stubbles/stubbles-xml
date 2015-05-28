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
     * @type  \stubbles\xml\serializer\XmlSerializer
     */
    protected $xmlSerializer;
    /**
     * xml stream writer to write serialization to
     *
     * @type  \stubbles\xml\XmlStreamWriter
     */
    protected $xmlStreamWriter;

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
     * @param   mixed  $data     data to serialize
     * @param   array  $tagName  name for root tag
     * @return  string
     */
    public function serializeToXml($data, $tagName = null)
    {
        return $this->xmlSerializer->serialize($data, $this->xmlStreamWriter, $tagName)
                                   ->asXml();
    }

    /**
     * serialize any data structure to XML
     *
     * @param   mixed  $data     data to serialize
     * @param   array  $tagName  name for root tag
     * @return  \DOMDocument
     */
    public function serializeToDom($data, $tagName = null)
    {
        return $this->xmlSerializer->serialize($data, $this->xmlStreamWriter, $tagName)
                                   ->asDOM();
    }
}
