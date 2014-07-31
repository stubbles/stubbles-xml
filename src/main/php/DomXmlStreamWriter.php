<?php
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
    protected $elementStack = [];

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
     * @return  \stubbles\xml\XmlStreamWriter
     */
    public function clear()
    {
        $this->doc          = new \DOMDocument($this->xmlVersion, $this->encoding);
        $this->elementStack = [];
        return $this;
    }

    /**
     * returns a list of features the implementation supports
     *
     * @return  int[]
     */
    protected function getFeatures()
    {
        return [XmlStreamWriter::FEATURE_AS_DOM,
                XmlStreamWriter::FEATURE_IMPORT_WRITER
        ];
    }

    /**
     * really writes an opening tag
     *
     * @param   string  $elementName
     */
    protected function doWriteStartElement($elementName)
    {
        $this->wrapStackHandling(function(\DOMDocument $doc, array &$elementStack, $payload)
                                 {
                                     $element = $doc->createElement($payload);
                                     if (count($elementStack) === 0) {
                                         $doc->appendChild($element);
                                     } else {
                                         $parent = end($elementStack);
                                         $parent->appendChild($element);
                                     }

                                     array_push($elementStack, $element);
                                 },
                                 $elementName,
                                 'start element: "' . $elementName . '"'
        );
    }

    /**
     * Write a text node
     *
     * @param   string  $data
     * @return  \stubbles\xml\XmlStreamWriter
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
     * @return  \stubbles\xml\XmlStreamWriter
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
     * @return  \stubbles\xml\XmlStreamWriter
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
     * @return  \stubbles\xml\XmlStreamWriter
     */
    public function writeProcessingInstruction($target, $data = '')
    {
        return $this->handleElementCreation(function(\DOMDocument $doc, $payload)
                                            {
                                                return $doc->createProcessingInstruction($payload['target'], $payload['data']);
                                            },
                                            ['target' => $target,
                                                  'data'   => $data
                                            ],
                                            'processing instruction'
        );
    }

    /**
     * Write an xml fragment
     *
     * @param   string  $fragment
     * @return  \stubbles\xml\XmlStreamWriter
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
     * @return  \stubbles\xml\XmlStreamWriter
     */
    public function writeAttribute($attributeName, $attributeValue)
    {
        return $this->wrapStackHandling(function(\DOMDocument $doc, array &$elementStack, $payload)
                                        {
                                            $currentElement = end($elementStack);
                                            $currentElement->setAttribute($payload['name'], $payload['value']);
                                        },
                                        ['name'  => $attributeName,
                                                'value' => $this->encode($attributeValue)
                                        ],
                                        'attributet: "' . $attributeName . ':' . $attributeValue . '"'
        );
    }

    /**
     * really writes an end element
     *
     * @throws  \stubbles\xml\XmlException
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
     * @return  \stubbles\xml\XmlStreamWriter
     * @throws  \stubbles\xml\XmlException
     */
    public function writeElement($elementName, array $attributes = [], $cdata = null)
    {
        $atts = [];
        foreach ($attributes as $name => $value) {
            $atts[$name] = $this->encode($value);
        }

        return $this->wrapStackHandling(function(\DOMDocument $doc, array &$elementStack, $payload)
                                        {
                                            $element = $doc->createElement($payload['elementName']);
                                            foreach ($payload['attributes'] as $attName => $attValue) {
                                                $element->setAttribute($attName, $attValue);
                                            }

                                            if (null !== $payload['cdata']) {
                                                $element->appendChild($doc->createTextNode($payload['cdata']));
                                            }

                                            if (count($elementStack) === 0) {
                                                $doc->appendChild($element);
                                            } else {
                                                $parent = end($elementStack);
                                                $parent->appendChild($element);
                                            }
                                        },
                                        ['elementName' => $elementName,
                                         'attributes'  => $atts,
                                         'cdata'       => $this->encode($cdata)
                                        ],
                                        'element: "' . $elementName . '"'
        );
    }

    /**
     * Import another stream
     *
     * @param   \stubbles\xml\XmlStreamWriter  $writer
     * @return  \stubbles\xml\XmlStreamWriter
     * @throws  \stubbles\xml\XmlException
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
     * wraps handling on element stack
     *
     * @param   \Closure  $stackHandling  function to work with element stack
     * @param   mixed     $payload        payload to pass to stack handling
     * @param   string    $type           type of stack handling
     * @return  \stubbles\xml\XmlStreamWriter
     * @throws  \stubbles\xml\XmlException
     */
    protected function wrapStackHandling(\Closure $stackHandling, $payload, $type)
    {
        try {
            libxml_use_internal_errors(true);
            $stackHandling($this->doc, $this->elementStack, $payload);
            $errors = libxml_get_errors();
            if (!empty($errors)) {
                libxml_clear_errors();
                throw new XmlException('Error writing "' . $type . '": ' . $this->convertLibXmlErrorsToString($errors));
            }
        } catch (\DOMException $e) {
            throw new XmlException('Error writing "' . $type, $e);
        }

        return $this;
    }

    /**
     * handles creation of a new element
     *
     * @param   \Closure  $createElement  function to create element
     * @param   mixed     $payload        payload to pass for element creation
     * @param   string    $type           type of element
     * @return  \stubbles\xml\XmlStreamWriter
     * @throws  \stubbles\xml\XmlException
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
        $messages = [];
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
