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
 * XML Stream Writer based on DOM.
 */
class DomXmlStreamWriter extends XmlStreamWriter
{
    /**
     * DOM Document
     *
     * @type  \DOMDocument
     */
    private $doc;
    /**
     * stack of all opened elements
     *
     * @type  array
     */
    private $openElements = [];

    /**
     * Create a new writer
     *
     * @param  string  $xmlVersion
     * @param  string  $encoding
     */
    public function __construct(string $xmlVersion = '1.0', string $encoding = 'UTF-8')
    {
        parent::__construct($xmlVersion, $encoding);
        $this->doc          = new \DOMDocument($xmlVersion, $encoding);
        $this->openElements = [$this->doc];
    }

    /**
     * Clears all previously written elements so that the document starts fresh.
     *
     * @return  \stubbles\xml\XmlStreamWriter
     */
    public function clear(): XmlStreamWriter
    {
        $this->doc          = new \DOMDocument($this->version(), $this->encoding());
        $this->openElements = [$this->doc];
        return parent::clear();
    }

    /**
     * returns a list of features the implementation supports
     *
     * @return  int[]
     */
    protected function features(): array
    {
        return [
                XmlStreamWriter::FEATURE_AS_DOM,
                XmlStreamWriter::FEATURE_IMPORT_WRITER
        ];
    }

    /**
     * really writes an opening tag
     *
     * @param  string  $elementName
     */
    protected function doWriteStartElement(string $elementName)
    {
        $this->append(
                function(\DOMNode $parent) use ($elementName)
                {
                    $element = $this->doc->createElement($elementName);
                    $parent->appendChild($element);
                    array_push($this->openElements, $element);
                },
                'start element: "' . $elementName . '"'
        );
    }

    /**
     * Write a text node
     *
     * @param   string  $data
     * @return  \stubbles\xml\XmlStreamWriter
     */
    public function writeText(string $data): XmlStreamWriter
    {
        return $this->append(
                function(\DOMNode $parent) use ($data)
                {
                    $parent->appendChild(
                            $this->doc->createTextNode($this->encode($data))
                    );
                },
                'text'
        );
    }

    /**
     * Write a cdata section
     *
     * @param   string  $cdata
     * @return  \stubbles\xml\XmlStreamWriter
     */
    public function writeCData(string $cdata): XmlStreamWriter
    {
        return $this->append(
                function(\DOMNode $parent) use ($cdata)
                {
                    $parent->appendChild(
                            $this->doc->createCDATASection($this->encode($cdata))
                    );
                },
                'cdata'
        );
    }

    /**
     * Write a comment
     *
     * @param   string  $comment
     * @return  \stubbles\xml\XmlStreamWriter
     */
    public function writeComment(string $comment): XmlStreamWriter
    {
        return $this->append(
                function(\DOMNode $parent) use ($comment)
                {
                    $parent->appendChild(
                            $this->doc->createComment($this->encode($comment))
                    );
                },
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
    public function writeProcessingInstruction(string $target, string $data = ''): XmlStreamWriter
    {
        return $this->append(
                function(\DOMNode $parent) use ($target, $data)
                {
                    $parent->appendChild($this->doc->createProcessingInstruction(
                            $target,
                            $data
                    ));
                },
                'processing instruction'
        );
    }

    /**
     * Write an xml fragment
     *
     * @param   string  $fragment
     * @return  \stubbles\xml\XmlStreamWriter
     */
    public function writeXmlFragment(string $fragment): XmlStreamWriter
    {
        return $this->append(
                function(\DOMNode $parent) use ($fragment)
                {
                    $fragmentNode = $this->doc->createDocumentFragment();
                    $fragmentNode->appendXML($fragment);
                    @$parent->appendChild($fragmentNode);
                },
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
    public function writeAttribute(string $attributeName, string $attributeValue): XmlStreamWriter
    {
        return $this->append(
                function(\DOMElement $parent) use ($attributeName, $attributeValue)
                {
                    $parent->setAttribute(
                            $attributeName,
                            $this->encode($attributeValue)
                    );
                },
                'attribute: "' . $attributeName . ':' . $attributeValue . '"'
        );
    }

    /**
     * really writes an end element
     *
     * @throws  \stubbles\xml\XmlException
     */
    protected function doWriteEndElement()
    {
        array_pop($this->openElements);
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
    public function writeElement(
            string $elementName,
            array $attributes = [],
            string $cdata     = null
    ): XmlStreamWriter {
        return $this->append(
                function(\DOMNode $parent) use ($elementName, $attributes, $cdata)
                {
                    $element = $this->doc->createElement($elementName);
                    foreach ($attributes as $name => $value) {
                        $element->setAttribute($name, $this->encode($value));
                    }

                    if (null !== $cdata) {
                        $element->appendChild($this->doc->createTextNode($this->encode($cdata)));
                    }

                    $parent->appendChild($element);
                },
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
    public function importStreamWriter(XmlStreamWriter $writer): XmlStreamWriter
    {
        return $this->append(
                function(\DOMNode $parent) use ($writer)
                {
                    $parent->appendChild($this->doc->importNode(
                            $writer->asDom()->documentElement,
                            true
                    ));
                },
                'imported nodes'
        );
    }

    /**
     * Return the XML as a DOM
     *
     * @return  \DOMDocument
     */
    public function asDom(): \DOMDocument
    {
        return $this->doc;
    }

    /**
     * Return the XML as a string
     *
     * @return  string
     */
    public function asXml(): string
    {
        return rtrim($this->doc->saveXML());
    }

    /**
     * wraps handling on element stack
     *
     * @param   \Closure  $appendTo  function to work with element stack
     * @param   string    $type      type of stack handling
     * @return  \stubbles\xml\XmlStreamWriter
     * @throws  \stubbles\xml\XmlException
     */
    private function append(\Closure $appendTo, string $type): XmlStreamWriter
    {
        libxml_use_internal_errors(true);
        try {
            $appendTo(end($this->openElements));
        } catch (\DOMException $e) {
            throw new XmlException('Error writing "' . $type, $e);
        }

        $errors = libxml_get_errors();
        if (!empty($errors)) {
            libxml_clear_errors();
            throw new XmlException(
                    'Error writing "' . $type . '": '
                    . implode(', ', array_map(
                            function($error) { return trim($error->message); },
                            $errors
                    ))
            );
        }

        return $this;
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
    private function encode(string $data): string
    {
        if (mb_detect_encoding($data, 'UTF-8, ISO-8859-1') === 'UTF-8') {
            return $data;
        }

        return utf8_encode($data);
    }
}
