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
namespace stubbles\xml;
/**
 * XML Stream Writer based on libxml
 */
class LibXmlStreamWriter extends XmlStreamWriter
{
    /**
     * Writer
     *
     * @type  \XMLWriter
     */
    private $writer;

    /**
     * Create a new writer
     *
     * @param  string  $xmlVersion
     * @param  string  $encoding
     */
    public function __construct(string $xmlVersion = '1.0', string $encoding = 'UTF-8')
    {
        parent::__construct($xmlVersion, $encoding);
        $this->writer = $this->createWriter($xmlVersion, $encoding);
    }

    private function createWriter($xmlVersion, $encoding): \XMLWriter
    {
        $writer = new \XMLWriter();
        $writer->openMemory();
        $writer->startDocument($xmlVersion, $encoding);
        $writer->setIndent(false);
        return $writer;
    }

    /**
     * Clears all previously written elements so that the document starts fresh.
     *
     * @return  \stubbles\xml\XmlStreamWriter
     */
    public function clear(): XmlStreamWriter
    {
        unset($this->writer);
        $this->writer = $this->createWriter($this->version(), $this->encoding());
        return parent::clear();
    }

    /**
     * returns a list of features the implementation supports
     *
     * @return  int[]
     */
    protected function features(): array
    {
        return [XmlStreamWriter::FEATURE_AS_DOM];
    }

    /**
     * really writes an opening tag
     *
     * @param  string  $elementName
     */
    protected function doWriteStartElement(string $elementName)
    {
        $this->writer->startElement($elementName);
    }

    /**
     * Write a text node
     *
     * @param   string  $data
     * @return  \stubbles\xml\XmlStreamWriter
     */
    public function writeText(string $data): XmlStreamWriter
    {
        $this->writer->text($data);
        return $this;
    }

    /**
     * Write a cdata section
     *
     * @param   string  $cdata
     * @return  \stubbles\xml\XmlStreamWriter
     */
    public function writeCData(string $cdata): XmlStreamWriter
    {
        $this->writer->writeCdata($cdata);
        return $this;
    }

    /**
     * Write a comment
     *
     * @param   string  $comment
     * @return  \stubbles\xml\XmlStreamWriter
     */
    public function writeComment(string $comment): XmlStreamWriter
    {
        $this->writer->writeComment($comment);
        return $this;
    }

    /**
     * Write a processing instruction
     *
     * @param   string  $target
     * @param   string  $data
     * @return  \stubbles\xml\XmlStreamWriter
     */
    public function writeProcessingInstruction(string $target, string $data = ''): XmlStreamWriter
    {
        $this->writer->writePi($target, $data);
        return $this;
    }

    /**
     * Write an xml fragment
     *
     * @param   string  $fragment
     * @return  \stubbles\xml\XmlStreamWriter
     */
    public function writeXmlFragment(string $fragment): XmlStreamWriter
    {
        $this->writer->writeRaw($fragment);
        return $this;
    }

    /**
     * Write an attribute
     *
     * @param   string  $attributeName
     * @param   string  $attributeValue
     * @return  \stubbles\xml\XmlStreamWriter
     */
    public function writeAttribute(string $attributeName, string $attributeValue): XmlStreamWriter
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
     * @return  \stubbles\xml\XmlStreamWriter
     */
    public function writeElement(
            string $elementName,
            array $attributes = [],
            string $cdata     = null
    ): XmlStreamWriter {
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
     * @param   \stubbles\xml\XmlStreamWriter  $writer
     * @throws  \BadMethodCallException
     */
    public function importStreamWriter(XmlStreamWriter $writer): XmlStreamWriter
    {
        throw new \BadMethodCallException('Can not import another stream writer.');
    }

    /**
     * Return the XML as a DOM
     *
     * @return  \DOMDocument
     */
    public function asDom(): \DOMDocument
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
    public function asXml(): string
    {
        return rtrim($this->writer->outputMemory());
    }
}
