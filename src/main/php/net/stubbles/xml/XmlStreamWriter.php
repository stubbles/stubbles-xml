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
use net\stubbles\lang\Object;
/**
 * Interface to create XML documents
 *
 * @ProvidedBy(net\stubbles\xml\XmlStreamWriterProvider.class)
 */
interface XmlStreamWriter extends Object
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
    public function getVersion();

    /**
     * returns the encoding used by the writer
     *
     * @return  string
     */
    public function getEncoding();

    /**
     * Checks, whether the implementation has a desired feature
     *
     * @param   int  $feature
     * @return  bool
     */
    public function hasFeature($feature);

    /**
     * Clear all data, that has been written
     */
    public function clear();

    /**
     * Write an opening tag
     *
     * @param  string  $elementName
     */
    public function writeStartElement($elementName);

    /**
     * Write a text node
     *
     * @param  string  $data
     */
    public function writeText($data);

    /**
     * Write a cdata section
     *
     * @param  string  $cdata
     */
    public function writeCData($cdata);

    /**
     * Write a comment
     *
     * @param  string  $comment
     */
    public function writeComment($comment);

    /**
     * Write a processing instruction
     *
     * @param  string  $target
     * @param  string  $data
     */
    public function writeProcessingInstruction($target, $data = '');

    /**
     * Write an xml fragment
     *
     * @param  string  $fragment
     */
    public function writeXmlFragment($fragment);

    /**
     * Write an attribute
     *
     * @param  string  $attributeName
     * @param  string  $attributeValue
     */
    public function writeAttribute($attributeName, $attributeValue);

    /**
     * Write an end element
     */
    public function writeEndElement();

    /**
     * Write a full element
     *
     * @param  string  $elementName
     * @param  array   $attributes
     * @param  string  $cdata
     */
    public function writeElement($elementName, array $attributes = array(), $cdata = null);

    /**
     * Import another stream
     *
     * @param  XmlStreamWriter  $writer
     */
    public function importStreamWriter(XmlStreamWriter $writer);

    /**
     * checks whether the document is finished meaning no open tags are left
     *
     * @return  bool
     */
    public function isFinished();

    /**
     * Return the XML as a string
     *
     * @return  string
     */
    public function asXml();

    /**
     * Return the XML as a DOM
     *
     * @return  \DOMDocument
     */
    public function asDom();
}
?>