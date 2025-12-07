<?php
declare(strict_types=1);
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace stubbles\xml;

use Closure;
use DOMDocument;
use DOMException;
use DOMNode;
use Override;

/**
 * XML Stream Writer based on DOM.
 */
class DomXmlStreamWriter extends XmlStreamWriter
{
    private DOMDocument $doc;
    /**
     * stack of all opened elements
     *
     * @var  DOMNode[]
     */
    private array $openElements = [];


    public function __construct(string $xmlVersion = '1.0', string $encoding = 'UTF-8')
    {
        parent::__construct($xmlVersion, $encoding);
        $this->doc          = new DOMDocument($xmlVersion, $encoding);
        $this->openElements = [$this->doc];
    }

    #[Override]
    public function clear(): XmlStreamWriter
    {
        $this->doc          = new DOMDocument($this->version(), $this->encoding());
        $this->openElements = [$this->doc];
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
        return [
            XmlStreamWriter::FEATURE_AS_DOM,
            XmlStreamWriter::FEATURE_IMPORT_WRITER
        ];
    }

    #[Override]
    protected function doWriteStartElement(string $elementName): void
    {
        $this->append(
            function(DOMNode $parent) use ($elementName)
            {
                $element = $this->doc->createElement($elementName);
                $parent->appendChild($element);
                array_push($this->openElements, $element);
            },
            'start element: "' . $elementName . '"'
        );
    }

    #[Override]
    public function writeText(string $data): XmlStreamWriter
    {
        return $this->append(
            function(DOMNode $parent) use ($data)
            {
                $parent->appendChild(
                        $this->doc->createTextNode($this->encode($data))
                );
            },
            'text'
        );
    }

    #[Override]
    public function writeCData(string $cdata): XmlStreamWriter
    {
        return $this->append(
            function(DOMNode $parent) use ($cdata)
            {
                $parent->appendChild(
                    $this->doc->createCDATASection($this->encode($cdata))
                );
            },
            'cdata'
        );
    }

    #[Override]
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

    #[Override]
    public function writeProcessingInstruction(string $target, string $data = ''): XmlStreamWriter
    {
        return $this->append(
            function(\DOMNode $parent) use ($target, $data)
            {
                $parent->appendChild(
                    $this->doc->createProcessingInstruction($target, $data)
                );
            },
            'processing instruction'
        );
    }

    #[Override]
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

    #[Override]
    public function writeAttribute(
        string $attributeName,
        string $attributeValue
    ): XmlStreamWriter {
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

    #[Override]
    protected function doWriteEndElement(): void
    {
        array_pop($this->openElements);
    }

    /**
     * @param   array<string,string>  $attributes
     * @throws  XmlException
     */
    #[Override]
    public function writeElement(
        string $elementName,
        array $attributes = [],
        ?string $cdata     = null
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
     * @throws  XmlException
     */
    #[Override]
    public function importStreamWriter(XmlStreamWriter $writer): XmlStreamWriter
    {
        return $this->append(
            function(DOMNode $parent) use ($writer)
            {
                $element = $writer->asDom()->documentElement;
                if (null === $element) {
                    // nothing to import
                    return;
                }

                $parent->appendChild($this->doc->importNode(
                    $element,
                    true
                ));
            },
            'imported nodes'
        );
    }

    #[Override]
    public function asDom(): DOMDocument
    {
        return $this->doc;
    }

    #[Override]
    public function asXml(): string
    {
        $xml = $this->doc->saveXML();
        if (false !== $xml) {
            return rtrim($xml);
        }
        
        $errors = libxml_get_errors();
        if (!empty($errors)) {
            libxml_clear_errors();
            throw new XmlException(
                'Error returning document as XML: '
                . implode(', ', array_map(
                    fn($error) => trim($error->message),
                    $errors
                ))
            );
        }

        throw new XmlException('Unknown error on returning document as XML');
    }

    /**
     * wraps handling on element stack
     *
     * @param   \Closure  $appendTo  function to work with element stack
     * @param   string    $type      type of stack handling
     * @return  \stubbles\xml\XmlStreamWriter
     * @throws  \stubbles\xml\XmlException
     */
    private function append(Closure $appendTo, string $type): XmlStreamWriter
    {
        libxml_use_internal_errors(true);
        try {
            $appendTo(end($this->openElements));
        } catch (DOMException $e) {
            throw new XmlException('Error writing "' . $type, $e);
        }

        $errors = libxml_get_errors();
        if (!empty($errors)) {
            libxml_clear_errors();
            throw new XmlException(
                'Error writing "' . $type . '": '
                . implode(', ', array_map(
                    fn($error) => trim($error->message),
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
     * @see  http://php.net/manual/en/function.dom-domdocument-save.php#67952
     */
    private function encode(string $data): string
    {
        $detectedEncoding = mb_detect_encoding($data, 'UTF-8, ISO-8859-1');
        if ($detectedEncoding === 'UTF-8') {
            return $data;
        }

        return mb_convert_encoding($data, 'UTF-8', $detectedEncoding);
    }
}
