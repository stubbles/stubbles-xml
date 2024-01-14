<?php
declare(strict_types=1);
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace stubbles\xml;

use DOMDocument;
use LogicException;

/**
 * Interface to create XML documents
 *
 * @ProvidedBy(stubbles\xml\XmlStreamWriterProvider.class)
 */
abstract class XmlStreamWriter
{
    /**
     * Is able to import an XmlStreamWriter
     *
     * @var int
     */
    const FEATURE_IMPORT_WRITER = 1;
    /**
     * Is able to export as DOM
     *
     * @var int
     */
    const FEATURE_AS_DOM = 2;
    /** depth, i.e. amount of opened tags */
    private int $depth        = 0;

    public function __construct(
        private string $xmlVersion = '1.0',
        private string $encoding = 'UTF-8'
    ) { }

    /**
     * returns the xml version used by the writer
     */
    public function version(): string
    {
        return $this->xmlVersion;
    }

    /**
     * returns the encoding used by the writer
     */
    public function encoding(): string
    {
        return $this->encoding;
    }

    /**
     * Checks, whether the implementation has a desired feature
     */
    public function hasFeature(int $feature): bool
    {
        return in_array($feature, $this->features());
    }

    /**
     * returns a list of features the implementation supports
     *
     * @return  int[]
     */
    abstract protected function features(): array;

    /**
     * Clears all previously written elements so that the document starts fresh.
     */
    public function clear(): self
    {
        $this->depth = 0;
        return $this;
    }

    /**
     * Write an opening tag
     */
    public function writeStartElement(string $elementName): self
    {
        $this->doWriteStartElement($elementName);
        $this->depth++;
        return $this;
    }

    /**
     * really writes an opening tag
     */
    abstract protected function doWriteStartElement(string $elementName): void;


    /**
     * Write a text node
     */
    abstract public function writeText(string $data): self;

    /**
     * Write a cdata section
     */
    abstract public function writeCData(string $cdata): self;

    /**
     * Write a comment
     */
    abstract public function writeComment(string $comment): self;

    /**
     * Write a processing instruction
     */
    abstract public function writeProcessingInstruction(
        string $target,
        string $data = ''
    ): self;

    /**
     * Write an xml fragment
     */
    abstract public function writeXmlFragment(string $fragment): self;

    /**
     * Write an attribute
     */
    abstract public function writeAttribute(
        string $attributeName,
        string $attributeValue
    ): self;

    /**
     * Write an end element
     *
     * @throws  LogicException  in case no element is open
     */
    public function writeEndElement(): self
    {
        if ($this->isFinished()) {
            throw new LogicException('Can not write end elements, no element open.');
        }

        $this->doWriteEndElement();
        $this->depth--;
        return $this;
    }

    /**
     *  really writes an end element
     */
    abstract protected function doWriteEndElement(): void;

    /**
     * Write a full element
     *
     * @param   array<string,string>  $attributes
     */
    abstract public function writeElement(
            string $elementName,
            array $attributes = [],
            string $cdata     = null
    ): self;

    /**
     * Import another stream
     */
    abstract public function importStreamWriter(XmlStreamWriter $writer): self;

    /**
     * checks whether the document is finished meaning no open tags are left
     */
    public function isFinished(): bool
    {
        return 0 === $this->depth;
    }

    /**
     * Return the XML as a string
     */
    abstract public function asXml(): string;

    /**
     * Return the XML as a DOM
     */
    abstract public function asDom(): DOMDocument;
}
