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
 * Abstract base class for XML stream writers.
 */
abstract class AbstractXmlStreamWriter
{
    /**
     * XML version
     *
     * @type  string
     */
    protected $xmlVersion;
    /**
     * encoding used by the writer
     *
     * @type  string
     */
    protected $encoding;
    /**
     * depth, i.e. amount of opened tags
     *
     * @type  int
     */
    private $depth        = 0;

    /**
     * returns the xml version used by the writer
     *
     * @return  string
     */
    public function getVersion(): string
    {
        return $this->xmlVersion;
    }

    /**
     * returns the encoding used by the writer
     *
     * @return  string
     */
    public function getEncoding(): string
    {
        return $this->encoding;
    }

    /**
     * Checks, whether the implementation has a desired feature
     *
     * @param   int   $feature
     * @return  bool
     */
    public function hasFeature(int $feature): bool
    {
        return in_array($feature, $this->getFeatures());
    }

    /**
     * returns a list of features the implementation supports
     *
     * @return  int[]
     */
    protected abstract function getFeatures(): array;

    /**
     * Write an opening tag
     *
     * @param   string  $elementName
     * @return  XmlStreamWriter
     */
    public function writeStartElement(string $elementName): XmlStreamWriter
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
     * Write an end element
     *
     * @return  XmlStreamWriter
     */
    public function writeEndElement(): XmlStreamWriter
    {
        $this->doWriteEndElement();
        $this->depth--;
        return $this;
    }

    /**
     *  really writes an end element
     */
    protected abstract function doWriteEndElement();

    /**
     * checks whether the document is finished meaning no open tags are left
     *
     * @return  bool
     */
    public function isFinished(): bool
    {
        return 0 === $this->depth;
    }
}
