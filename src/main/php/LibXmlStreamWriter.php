<?php
declare(strict_types=1);
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace stubbles\xml;

use BadMethodCallException;
use DOMDocument;
use Override;
use XMLWriter;

/**
 * XML Stream Writer based on libxml
 */
class LibXmlStreamWriter extends XmlStreamWriter
{
    private XMLWriter $writer;

    public function __construct(string $xmlVersion = '1.0', string $encoding = 'UTF-8')
    {
        parent::__construct($xmlVersion, $encoding);
        $this->writer = $this->createWriter($xmlVersion, $encoding);
    }

    private function createWriter(string $xmlVersion, string $encoding): XMLWriter
    {
        $writer = new XMLWriter();
        $writer->openMemory();
        $writer->startDocument($xmlVersion, $encoding);
        $writer->setIndent(false);
        return $writer;
    }

    #[Override]
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
    #[Override]
    protected function features(): array
    {
        return [XmlStreamWriter::FEATURE_AS_DOM];
    }

    #[Override]
    protected function doWriteStartElement(string $elementName): void
    {
        $this->writer->startElement($elementName);
    }

    #[Override]
    public function writeText(string $data): XmlStreamWriter
    {
        $this->writer->text($data);
        return $this;
    }

    #[Override]
    public function writeCData(string $cdata): XmlStreamWriter
    {
        $this->writer->writeCdata($cdata);
        return $this;
    }

    #[Override]
    public function writeComment(string $comment): XmlStreamWriter
    {
        $this->writer->writeComment($comment);
        return $this;
    }

    #[Override]
    public function writeProcessingInstruction(string $target, string $data = ''): XmlStreamWriter
    {
        $this->writer->writePi($target, $data);
        return $this;
    }

    #[Override]
    public function writeXmlFragment(string $fragment): XmlStreamWriter
    {
        $this->writer->writeRaw($fragment);
        return $this;
    }

    #[Override]
    public function writeAttribute(string $attributeName, string $attributeValue): XmlStreamWriter
    {
        $this->writer->writeAttribute($attributeName, $attributeValue);
        return $this;
    }

    #[Override]
    protected function doWriteEndElement(): void
    {
        $this->writer->endElement();
    }

    /**
     * @param  array<string,string>  $attributes
     */
    #[Override]
    public function writeElement(
        string $elementName,
        array $attributes = [],
        ?string $cdata     = null
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
     * @throws  BadMethodCallException
     */
    #[Override]
    public function importStreamWriter(XmlStreamWriter $writer): XmlStreamWriter
    {
        throw new BadMethodCallException('Can not import another stream writer.');
    }

    #[Override]
    public function asDom(): DOMDocument
    {
        $doc = new DOMDocument();
        $doc->loadXML($this->writer->outputMemory());
        return $doc;
    }

    #[Override]
    public function asXml(): string
    {
        return rtrim($this->writer->outputMemory());
    }
}
