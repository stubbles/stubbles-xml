<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  net\stubbles\xml
 */
namespace net\stubbles\xml;
use net\stubbles\lang\exception\MethodNotSupportedException;
/**
 * XML Stream Writer based on libxml
 */
class LibXmlStreamWriter extends AbstractXmlStreamWriter implements XmlStreamWriter
{
    /**
     * List of supported features
     *
     * @type  array
     */
    protected $features = array(XmlStreamWriter::FEATURE_AS_DOM);
    /**
     * Writer
     *
     * @type  XMLWriter
     */
    protected $writer;

    /**
     * Create a new writer
     *
     * @param  string  $xmlVersion
     * @param  string  $encoding
     */
    public function __construct($xmlVersion = '1.0', $encoding = 'UTF-8')
    {
        $this->xmlVersion = $xmlVersion;
        $this->encoding   = $encoding;
        $this->writer     = new \XMLWriter();
        $this->writer->openMemory();
        $this->writer->startDocument($xmlVersion, $encoding);
        $this->writer->setIndent(false);
    }

    /**
     * Clear all data, that has been written
     */
    public function clear()
    {
        unset($this->writer);
        $this->writer = new \XMLWriter();
        $this->writer->openMemory();
        $this->writer->startDocument($this->xmlVersion, $this->encoding);
        $this->writer->setIndent(false);
    }

    /**
     * really writes an opening tag
     *
     * @param  string  $elementName
     */
    protected function doWriteStartElement($elementName)
    {
        $this->writer->startElement($elementName);
    }

    /**
     * Write a text node
     *
     * @param  string  $data
     */
    public function writeText($data)
    {
        $this->writer->text($data);
    }

    /**
     * Write a cdata section
     *
     * @param  string  $cdata
     */
    public function writeCData($cdata)
    {
        $this->writer->writeCdata($cdata);
    }

    /**
     * Write a comment
     *
     * @param  string  $comment
     */
    public function writeComment($comment)
    {
        $this->writer->writeComment($comment);
    }

    /**
     * Write a processing instruction
     *
     * @param  string  $target
     * @param  string  $data
     */
    public function writeProcessingInstruction($target, $data = '')
    {
        $this->writer->writePi($target, $data);
    }

    /**
     * Write an xml fragment
     *
     * @param  string  $fragment
     */
    public function writeXmlFragment($fragment)
    {
        $this->writer->writeRaw($fragment);
    }

    /**
     * Write an attribute
     *
     * @param  string  $attributeName
     * @param  string  $attributeValue
     */
    public function writeAttribute($attributeName, $attributeValue)
    {
        $this->writer->writeAttribute($attributeName, $attributeValue);
    }

    /**
     * really writes an end element
     */
    protected function doWriteEndElement()
    {
        $this->writer->endElement();
    }

    /**
     * Write a full element
     *
     * @param  string  $elementName
     * @param  array   $attributes
     * @param  string  $cdata
     */
    public function writeElement($elementName, array $attributes = array(), $cdata = null)
    {
        $this->writeStartElement($elementName);
        foreach ($attributes as $attName => $attValue) {
            $this->writeAttribute($attName, $attValue);
        }

        if (null !== $cdata) {
            $this->writeText($cdata);
        }

        $this->writeEndElement();
    }

    /**
     * Import another stream
     *
     * @param   XmlStreamWriter  $writer
     * @throws  MethodNotSupportedException
     */
    public function importStreamWriter(XmlStreamWriter $writer)
    {
        throw new MethodNotSupportedException('Can not import another stream writer.');
    }

    /**
     * Return the XML as a DOM
     *
     * @return  \DOMDocument
     */
    public function asDom()
    {
        $doc = new \DOMDocument();
        $doc->loadXML($this->writer->outputMemory());
        return $doc;
    }

    /**
     * Return the XML as a string
     *
     * @return  string
     */
    public function asXml()
    {
        return rtrim($this->writer->outputMemory());
    }
}
?>