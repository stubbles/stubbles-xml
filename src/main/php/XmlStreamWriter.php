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
 * Interface to create XML documents
 *
 * @ProvidedBy(stubbles\xml\XmlStreamWriterProvider.class)
 */
interface XmlStreamWriter
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
     * returns the xml version used by the writer
     *
     * @return  string
     */
    public function getVersion(): string;

    /**
     * returns the encoding used by the writer
     *
     * @return  string
     */
    public function getEncoding(): string;

    /**
     * Checks, whether the implementation has a desired feature
     *
     * @param   int  $feature
     * @return  bool
     */
    public function hasFeature(int $feature): bool;

    /**
     * Clears all previously written elements so that the document starts fresh.
     *
     * @return  \stubbles\xml\XmlStreamWriter
     */
    public function clear(): self;

    /**
     * Write an opening tag
     *
     * @param   string  $elementName
     * @return  \stubbles\xml\XmlStreamWriter
     */
    public function writeStartElement(string $elementName): self;

    /**
     * Write a text node
     *
     * @param   string  $data
     * @return  \stubbles\xml\XmlStreamWriter
     */
    public function writeText(string $data): self;

    /**
     * Write a cdata section
     *
     * @param   string  $cdata
     * @return  \stubbles\xml\XmlStreamWriter
     */
    public function writeCData(string $cdata): self;

    /**
     * Write a comment
     *
     * @param   string  $comment
     * @return  \stubbles\xml\XmlStreamWriter
     */
    public function writeComment(string $comment): self;

    /**
     * Write a processing instruction
     *
     * @param   string  $target
     * @param   string  $data
     * @return  \stubbles\xml\XmlStreamWriter
     */
    public function writeProcessingInstruction(string $target, string $data = ''): self;

    /**
     * Write an xml fragment
     *
     * @param   string  $fragment
     * @return  \stubbles\xml\XmlStreamWriter
     */
    public function writeXmlFragment(string $fragment): self;

    /**
     * Write an attribute
     *
     * @param   string  $attributeName
     * @param   string  $attributeValue
     * @return  \stubbles\xml\XmlStreamWriter
     */
    public function writeAttribute(string $attributeName, string $attributeValue): self;

    /**
     * Write an end element
     *
     * @return  \stubbles\xml\XmlStreamWriter
     */
    public function writeEndElement(): self;

    /**
     * Write a full element
     *
     * @param   string  $elementName
     * @param   array   $attributes
     * @param   string  $cdata
     * @return  \stubbles\xml\XmlStreamWriter
     */
    public function writeElement(string $elementName, array $attributes = [], string $cdata = null): self;

    /**
     * Import another stream
     *
     * @param   \stubbles\xml\XmlStreamWriter  $writer
     * @return  \stubbles\xml\XmlStreamWriter
     */
    public function importStreamWriter(XmlStreamWriter $writer): self;

    /**
     * checks whether the document is finished meaning no open tags are left
     *
     * @return  bool
     */
    public function isFinished(): bool;

    /**
     * Return the XML as a string
     *
     * @return  string
     */
    public function asXml(): string;

    /**
     * Return the XML as a DOM
     *
     * @return  \DOMDocument
     */
    public function asDom(): \DOMDocument;
}
