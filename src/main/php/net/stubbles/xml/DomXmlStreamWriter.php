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
/**
 * XML Stream Writer based on DOM.
 */
class DomXmlStreamWriter extends AbstractXmlStreamWriter implements XmlStreamWriter
{
    /**
     * DOM Document
     *
     * @type  \DOMDocument
     */
    protected $doc;
    /**
     * Stores al opened elements
     *
     * @type  array
     */
    protected $elementStack = array();

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
        $this->doc        = new \DOMDocument($xmlVersion, $encoding);
    }

    /**
     * Clears all previously written elements so that the document starts fresh.
     *
     * @return  XmlStreamWriter
     */
    public function clear()
    {
        $this->doc          = new \DOMDocument($this->xmlVersion, $this->encoding);
        $this->elementStack = array();
        return $this;
    }

    /**
     * returns a list of features the implementation supports
     *
     * @return  int[]
     */
    protected function getFeatures()
    {
        return array(XmlStreamWriter::FEATURE_AS_DOM,
                     XmlStreamWriter::FEATURE_IMPORT_WRITER
        );
    }

    /**
     * really writes an opening tag
     *
     * @param   string  $elementName
     * @throws  XmlException
     */
    protected function doWriteStartElement($elementName)
    {
        try {
            libxml_use_internal_errors(true);
            $element = $this->doc->createElement($elementName);
            if (count($this->elementStack) == 0) {
                $this->doc->appendChild($element);
            } else {
                $parent = end($this->elementStack);
                $parent->appendChild($element);
            }

            array_push($this->elementStack, $element);
            $errors = libxml_get_errors();
            if (!empty($errors)) {
                libxml_clear_errors();
                throw new XmlException('Error writing start element: "' . $elementName . '": ' . $this->convertLibXmlErrorsToString($errors));
            }
        } catch (\DOMException $e) {
            throw new XmlException('Error writing start element "' . $elementName . '".', $e);
        }
    }

    /**
     * Write a text node
     *
     * @param   string  $data
     * @return  XmlStreamWriter
     */
    public function writeText($data)
    {
        return $this->handleElementCreation(function(\DOMDocument $doc, $payload)
                                            {
                                                return $doc->createTextNode($payload);
                                            },
                                            $this->encode($data),
                                            'text'
        );
    }

    /**
     * Write a cdata section
     *
     * @param   string  $cdata
     * @return  XmlStreamWriter
     */
    public function writeCData($cdata)
    {
        return $this->handleElementCreation(function(\DOMDocument $doc, $payload)
                                            {
                                                return $doc->createCDATASection($payload);
                                            },
                                            $this->encode($cdata),
                                            'cdata'
        );
    }

    /**
     * Write a comment
     *
     * @param   string  $comment
     * @return  XmlStreamWriter
     */
    public function writeComment($comment)
    {
        return $this->handleElementCreation(function(\DOMDocument $doc, $payload)
                                            {
                                                return $doc->createComment($payload);
                                            },
                                            $this->encode($comment),
                                            'comment'
        );
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
        return $this->handleElementCreation(function(\DOMDocument $doc, $payload)
                                            {
                                                return $doc->createProcessingInstruction($payload['target'], $payload['data']);
                                            },
                                            array('target' => $target,
                                                  'data'   => $data
                                            ),
                                            'processing instruction'
        );
    }

    /**
     * Write an xml fragment
     *
     * @param   string  $fragment
     * @return  XmlStreamWriter
     * @throws  XmlException
     */
    public function writeXmlFragment($fragment)
    {
        return $this->handleElementCreation(function(\DOMDocument $doc, $payload)
                                            {
                                                $fragmentNode = $doc->createDocumentFragment();
                                                $fragmentNode->appendXML($payload);
                                                return $fragmentNode;
                                            },
                                            $fragment,
                                            'document fragment'
        );
    }

    /**
     * Write an attribute
     *
     * @param   string  $attributeName
     * @param   string  $attributeValue
     * @return  XmlStreamWriter
     * @throws  XmlException
     */
    public function writeAttribute($attributeName, $attributeValue)
    {
        try {
            libxml_use_internal_errors(true);
            $currentElement = end($this->elementStack);
            $currentElement->setAttribute($attributeName, $this->encode($attributeValue));
            $errors = libxml_get_errors();
            if (!empty($errors)) {
                libxml_clear_errors();
                throw new XmlException('Error writing attribute:  "' . $attributeName . ':' . $attributeValue . '":' . $this->convertLibXmlErrorsToString($errors));
            }
        } catch (\DOMException $e) {
            throw new XmlException('Error writing attribute "' . $attributeName . ':' . $attributeValue . '".', $e);
        }

        return $this;
    }

    /**
     * really writes an end element
     *
     * @throws  XmlException
     */
    protected function doWriteEndElement()
    {
        if (count($this->elementStack) === 0) {
            throw new XmlException('No open element available.');
        }

        array_pop($this->elementStack);
    }

    /**
     * Write a full element
     *
     * @param   string  $elementName
     * @param   array   $attributes
     * @param   string  $cdata
     * @return  XmlStreamWriter
     * @throws  XmlException
     */
    public function writeElement($elementName, array $attributes = array(), $cdata = null)
    {
        try {
            libxml_use_internal_errors(true);
            $element = $this->doc->createElement($elementName);
            foreach ($attributes as $attName => $attValue) {
                $element->setAttribute($attName, $this->encode($attValue));
            }

            if (null !== $cdata) {
                $element->appendChild($this->doc->createTextNode($cdata));
            }

            if (count($this->elementStack) == 0) {
                $this->doc->appendChild($element);
            } else {
                $parent = end($this->elementStack);
                $parent->appendChild($element);
            }

            $errors = libxml_get_errors();
            if (!empty($errors)) {
                libxml_clear_errors();
                throw new XmlException('Error writing element: "' . $elementName . '":' . $this->convertLibXmlErrorsToString($errors));
            }
        } catch (\DOMException $e) {
            throw new XmlException('Error writing element"' . $elementName . '".', $e);
        }

        return $this;
    }

    /**
     * Import another stream
     *
     * @param   XmlStreamWriter  $writer
     * @return  XmlStreamWriter
     * @throws  XmlException
     */
    public function importStreamWriter(XmlStreamWriter $writer)
    {
        return $this->handleElementCreation(function(\DOMDocument $doc, $payload)
                                            {
                                                return $doc->importNode($payload, true);
                                            },
                                            $writer->asDom()->documentElement,
                                            'imported nodes'
        );
    }

    /**
     * Return the XML as a DOM
     *
     * @return  \DOMDocument
     */
    public function asDom()
    {
        return $this->doc;
    }

    /**
     * Return the XML as a string
     *
     * @return  string
     */
    public function asXml()
    {
        return rtrim($this->doc->saveXML());
    }

    /**
     * handles creation of a new element
     *
     * @param   \Closure  $createElement  function to create element
     * @param   string    $payload        payload to pass for element creation
     * @param   string    $type           type of element
     * @return  DomXmlStreamWriter
     * @throws  XmlException
     */
    protected function handleElementCreation(\Closure $createElement, $payload, $type)
    {
        if (count($this->elementStack) < 1) {
            throw new XmlException('No tag is currently open, you need to call writeStartElement() first.');
        }

        try {
            libxml_use_internal_errors(true);
            $current = end($this->elementStack);
            @$current->appendChild($createElement($this->doc, $payload));
            $errors = libxml_get_errors();
            if (!empty($errors)) {
                libxml_clear_errors();
                throw new XmlException('Error writing ' . $type . ': ' . $this->convertLibXmlErrorsToString($errors));
            }
        } catch (\DOMException $e) {
            throw new XmlException('Error writing ' . $type, $e);
        }

        return $this;
    }

    /**
     * Converts all errors to a string
     *
     * @param   array   $errors  list of errors to convert
     * @return  string
     */
    protected function convertLibXmlErrorsToString($errors)
    {
        $messages = array();
        foreach ($errors as $error) {
            $messages[] = trim($error->message);
        }

        return implode(', ', $messages);
    }

    /**
     * helper method to transform data into correct encoding
     *
     * Data has to be encoded even if document encoding is not UTF-8.
     *
     * @param   string  $data
     * @return  string
     * @see     http://php.net/manual/en/function.dom-domdocument-save.php#67952
     */
    protected function encode($data)
    {
        if (mb_detect_encoding($data, 'UTF-8, ISO-8859-1') === 'UTF-8') {
            return $data;
        }

        return utf8_encode($data);
    }
}
?>