<?php
declare(strict_types=1);
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace stubbles\xml;
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
     * @type int
     */
    const FEATURE_IMPORT_WRITER = 1;
    /**
     * Is able to export as DOM
     *
     * @type int
     */
    const FEATURE_AS_DOM = 2;
    /**
     * XML version
     *
     * @type  string
     */
    private $xmlVersion;
    /**
     * encoding used by the writer
     *
     * @type  string
     */
    private $encoding;
    /**
     * depth, i.e. amount of opened tags
     *
     * @type  int
     */
    private $depth        = 0;

    /**
     * Create a new writer
     *
     * @param  string  $xmlVersion
     * @param  string  $encoding
     */
    public function __construct(string $xmlVersion = '1.0', string $encoding = 'UTF-8')
    {
        $this->xmlVersion = $xmlVersion;
        $this->encoding   = $encoding;
    }

    /**
     * returns the xml version used by the writer
     *
     * @return  string
     */
    public function version(): string
    {
        return $this->xmlVersion;
    }

    /**
     * returns the encoding used by the writer
     *
     * @return  string
     */
    public function encoding(): string
    {
        return $this->encoding;
    }

    /**
     * Checks, whether the implementation has a desired feature
     *
     * @param   int  $feature
     * @return  bool
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
    protected abstract function features(): array;

    /**
     * Clears all previously written elements so that the document starts fresh.
     *
     * @return  \stubbles\xml\XmlStreamWriter
     */
    public function clear(): self
    {
        $this->depth = 0;
        return $this;
    }

    /**
     * Write an opening tag
     *
     * @param   string  $elementName
     * @return  \stubbles\xml\XmlStreamWriter
     */
    public function writeStartElement(string $elementName): self
    {
        $this->doWriteStartElement($elementName);
        $this->depth++;
        return $this;
    }

    /**
     * really writes an opening tag
     *
     * @param  string  $elementName
     */
    protected abstract function doWriteStartElement(string $elementName);


    /**
     * Write a text node
     *
     * @param   string  $data
     * @return  \stubbles\xml\XmlStreamWriter
     */
    public abstract function writeText(string $data): self;

    /**
     * Write a cdata section
     *
     * @param   string  $cdata
     * @return  \stubbles\xml\XmlStreamWriter
     */
    public abstract function writeCData(string $cdata): self;

    /**
     * Write a comment
     *
     * @param   string  $comment
     * @return  \stubbles\xml\XmlStreamWriter
     */
    public abstract function writeComment(string $comment): self;

    /**
     * Write a processing instruction
     *
     * @param   string  $target
     * @param   string  $data
     * @return  \stubbles\xml\XmlStreamWriter
     */
    public abstract function writeProcessingInstruction(string $target, string $data = ''): self;

    /**
     * Write an xml fragment
     *
     * @param   string  $fragment
     * @return  \stubbles\xml\XmlStreamWriter
     */
    public abstract function writeXmlFragment(string $fragment): self;

    /**
     * Write an attribute
     *
     * @param   string  $attributeName
     * @param   string  $attributeValue
     * @return  \stubbles\xml\XmlStreamWriter
     */
    public abstract function writeAttribute(string $attributeName, string $attributeValue): self;

    /**
     * Write an end element
     *
     * @return  \stubbles\xml\XmlStreamWriter
     * @throws  \LogicException  in case no element is open
     */
    public function writeEndElement(): self
    {
        if ($this->isFinished()) {
            throw new \LogicException('Can not write end elements, no element open.');
        }

        $this->doWriteEndElement();
        $this->depth--;
        return $this;
    }

    /**
     *  really writes an end element
     */
    protected abstract function doWriteEndElement();

    /**
     * Write a full element
     *
     * @param   string  $elementName
     * @param   array   $attributes
     * @param   string  $cdata
     * @return  \stubbles\xml\XmlStreamWriter
     */
    public abstract function writeElement(
            string $elementName,
            array $attributes = [],
            string $cdata     = null
    ): self;

    /**
     * Import another stream
     *
     * @param   \stubbles\xml\XmlStreamWriter  $writer
     * @return  \stubbles\xml\XmlStreamWriter
     */
    public abstract function importStreamWriter(XmlStreamWriter $writer): self;

    /**
     * checks whether the document is finished meaning no open tags are left
     *
     * @return  bool
     */
    public function isFinished(): bool
    {
        return 0 === $this->depth;
    }

    /**
     * Return the XML as a string
     *
     * @return  string
     */
    public abstract function asXml(): string;

    /**
     * Return the XML as a DOM
     *
     * @return  \DOMDocument
     */
    public abstract function asDom(): \DOMDocument;
}
