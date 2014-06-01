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
use stubbles\lang\exception\MethodNotSupportedException;
/**
 * XML Stream Writer based on libxml
 */
class LibXmlStreamWriter extends AbstractXmlStreamWriter implements XmlStreamWriter
{
    /**
     * Writer
     *
     * @type  \XMLWriter
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
     * Clears all previously written elements so that the document starts fresh.
     *
     * @return  XmlStreamWriter
     */
    public function clear()
    {
        unset($this->writer);
        $this->writer = new \XMLWriter();
        $this->writer->openMemory();
        $this->writer->startDocument($this->xmlVersion, $this->encoding);
        $this->writer->setIndent(false);
        return $this;
    }

    /**
     * returns a list of features the implementation supports
     *
     * @return  int[]
     */
    protected function getFeatures()
    {
        return [XmlStreamWriter::FEATURE_AS_DOM];
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
     * @param   string  $data
     * @return  XmlStreamWriter
     */
    public function writeText($data)
    {
        $this->writer->text($data);
        return $this;
    }

    /**
     * Write a cdata section
     *
     * @param   string  $cdata
     * @return  XmlStreamWriter
     */
    public function writeCData($cdata)
    {
        $this->writer->writeCdata($cdata);
        return $this;
    }

    /**
     * Write a comment
     *
     * @param   string  $comment
     * @return  XmlStreamWriter
     */
    public function writeComment($comment)
    {
        $this->writer->writeComment($comment);
        return $this;
    }

    /**
     * Write a processing instruction
     *
     * @param   string  $target
     * @param   string  $data
     * @return  XmlStreamWriter
     */
    public function writeProcessingInstruction($target, $data = '')
    {
        $this->writer->writePi($target, $data);
        return $this;
    }

    /**
     * Write an xml fragment
     *
     * @param   string  $fragment
     * @return  XmlStreamWriter
     */
    public function writeXmlFragment($fragment)
    {
        $this->writer->writeRaw($fragment);
        return $this;
    }

    /**
     * Write an attribute
     *
     * @param   string  $attributeName
     * @param   string  $attributeValue
     * @return  XmlStreamWriter
     */
    public function writeAttribute($attributeName, $attributeValue)
    {
        $this->writer->writeAttribute($attributeName, $attributeValue);
        return $this;
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
     * @param   string  $elementName
     * @param   array   $attributes
     * @param   string  $cdata
     * @return  XmlStreamWriter
     */
    public function writeElement($elementName, array $attributes = [], $cdata = null)
    {
        $this->writeStartElement($elementName);
        foreach ($attributes as $attName => $attValue) {
            $this->writeAttribute($attName, $attValue);
        }

        if (null !== $cdata) {
            $this->writeText($cdata);
        }

        $this->writeEndElement();
        return $this;
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
