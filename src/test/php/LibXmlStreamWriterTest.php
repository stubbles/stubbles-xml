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

use function bovigo\assert\assert;
use function bovigo\assert\assertFalse;
use function bovigo\assert\assertTrue;
use function bovigo\assert\expect;
use function bovigo\assert\predicate\equals;
use function bovigo\assert\predicate\isInstanceOf;
/**
 * Test for stubbles\xml\LibXmlStreamWriter.
 *
 * @group  xml
 * @group  xml_core
 */
class LibXmlStreamWriterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * instance to test
     *
     * @type  LibXmlStreamWriter
     */
    protected $writer;

    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->writer = new LibXmlStreamWriter();
    }

    /**
     * @test
     */
    public function emptyDocumentContainsXmlHeader()
    {
        assert(
                $this->writer->asXml(),
                equals('<?xml version="1.0" encoding="UTF-8"?>')
        );
    }

    /**
     * @test
     */
    public function canCreateDocumentWithOtherXmlVersion()
    {
        $writer = new LibXmlStreamWriter('1.1');
        assert(
                $writer->asXml(),
                equals('<?xml version="1.1" encoding="UTF-8"?>')
        );
    }

    /**
     * @test
     */
    public function canCreateDocumentWithIso()
    {
        $writer = new LibXmlStreamWriter('1.0', 'ISO-8859-1');
        assert(
                $writer->asXml(),
                equals('<?xml version="1.0" encoding="ISO-8859-1"?>')
        );
    }

    /**
     * @test
     */
    public function canCreateDocumentWithOtherVersionAndIso()
    {
        $writer = new LibXmlStreamWriter('1.1', 'ISO-8859-1');
        assert(
                $writer->asXml(),
                equals('<?xml version="1.1" encoding="ISO-8859-1"?>')
        );
    }

    /**
     * @test
     */
    public function hasVersion1_0ByDefault()
    {
        assert($this->writer->getVersion(), equals('1.0'));
    }

    /**
     * @test
     */
    public function hasUtf8EncodingByDefault()
    {
        assert($this->writer->getEncoding(), equals('UTF-8'));
    }

    /**
     * @test
     */
    public function reportsOtherVersion()
    {
        $writer = new LibXmlStreamWriter('1.1', 'ISO-8859-1');
        assert($writer->getVersion(), equals('1.1'));
    }

    /**
     * @test
     */
    public function reportsOtherEncoding()
    {
        $writer = new LibXmlStreamWriter('1.1', 'ISO-8859-1');
        assert($writer->getEncoding(), equals('ISO-8859-1'));
    }

    /**
     * @test
     */
    public function canWriteNestedElements()
    {
        assert(
                $this->writer->writeStartElement('root')
                        ->writeStartElement('foo')
                        ->writeEndElement()
                        ->writeStartElement('bar')
                        ->writeEndElement()
                        ->writeEndElement()
                        ->asXml(),
                equals('<?xml version="1.0" encoding="UTF-8"?>'
                . "\n<root><foo/><bar/></root>")
        );
    }

    /**
     * @test
     */
    public function writeElement()
    {
        assert(
                $this->writer->writeElement('foo', ['att' => 'value'], 'content')
                        ->asXml(),
                equals('<?xml version="1.0" encoding="UTF-8"?>' . "\n"#
                 . '<foo att="value">content</foo>')
        );
    }

    /**
     * @test
     */
    public function writeElementWithGermanUmlautsUtf8()
    {
        assert(
                $this->writer->writeElement('foo', ['att' => 'hääää'], 'content')
                        ->asXml(),
                equals('<?xml version="1.0" encoding="UTF-8"?>' . "\n"
                . '<foo att="hääää">content</foo>')
        );
    }

    /**
     * @test
     */
    public function writeAttributesAddsAtributesToCurrentElement()
    {
        assert(
                $this->writer->writeStartElement('root')
                        ->writeStartElement('foo')
                        ->writeAttribute('bar', '42')
                        ->writeEndElement()
                        ->writeEndElement()
                        ->asXml(),
                equals('<?xml version="1.0" encoding="UTF-8"?>' . "\n"
                . '<root><foo bar="42"/></root>')
        );
    }

    /**
     * @test
     */
    public function writeAttributesWithGermanUmlautsUtf8()
    {
        assert(
                $this->writer->writeStartElement('root')
                        ->writeStartElement('foo')
                        ->writeAttribute('bar', 'hääää')
                        ->writeEndElement()
                        ->writeEndElement()
                        ->asXml(),
                equals('<?xml version="1.0" encoding="UTF-8"?>' . "\n"
                . '<root><foo bar="hääää"/></root>')
        );
    }

    /**
     * @test
     */
    public function writeTextAddsTextIntoCurrentElement()
    {
        assert(
                $this->writer->writeStartElement('root')
                        ->writeText('This is text.')
                        ->writeEndElement()
                        ->asXml(),
                equals('<?xml version="1.0" encoding="UTF-8"?>' . "\n"
                . '<root>This is text.</root>')
        );
    }

    /**
     * @test
     */
    public function writeTextWithGermanUmlautsInUtf8()
    {
        assert(
                $this->writer->writeStartElement('root')
                        ->writeText('This is text containing äöü.')
                        ->writeEndElement()
                        ->asXml(),
                equals('<?xml version="1.0" encoding="UTF-8"?>' . "\n"
                . '<root>This is text containing äöü.</root>')
        );
    }

    /**
     * @test
     */
    public function writeCDataAddsCdata()
    {
        assert(
                $this->writer->writeStartElement('root')
                        ->writeCData('This is text.')
                        ->writeEndElement()
                        ->asXml(),
                equals('<?xml version="1.0" encoding="UTF-8"?>' . "\n"
                . '<root><![CDATA[This is text.]]></root>')
        );
    }

    /**
     * @test
     */
    public function writeCDataWithGermanUmlautsInUtf8()
    {
        assert(
                $this->writer->writeStartElement('root')
                        ->writeCData('This is text containing äöü.')
                        ->writeEndElement()
                        ->asXml(),
                equals('<?xml version="1.0" encoding="UTF-8"?>' . "\n"
                . '<root><![CDATA[This is text containing äöü.]]></root>')
        );
    }

    /**
     * @test
     */
    public function writeCommentAddsComment()
    {
        assert(
                $this->writer->writeStartElement('root')
                        ->writeComment('This is a comment.')
                        ->writeEndElement()
                        ->asXml(),
                equals('<?xml version="1.0" encoding="UTF-8"?>' . "\n"
                . '<root><!--This is a comment.--></root>')
        );
    }

    /**
     * @test
     */
    public function commentWithGermanUmlautsInUtf8()
    {
        assert(
                $this->writer->writeStartElement('root')
                        ->writeComment('This is a comment containing äöü.')
                        ->writeEndElement()
                        ->asXml(),
                equals('<?xml version="1.0" encoding="UTF-8"?>' . "\n"
                . '<root><!--This is a comment containing äöü.--></root>')
        );
    }

    /**
     * @test
     */
    public function processingInstructionCanBeAdded()
    {
        assert(
                $this->writer->writeStartElement('root')
                        ->writeProcessingInstruction('php', 'phpinfo();')
                        ->writeEndElement()
                        ->asXml(),
                equals('<?xml version="1.0" encoding="UTF-8"?>' . "\n"
                . '<root><?php phpinfo();?></root>')
        );
    }

    /**
     * @test
     */
    public function xmlFragmentCanBeAdded()
    {
        assert(
                $this->writer->writeStartElement('root')
                        ->writeXmlFragment('<foo bar="true"/>')
                        ->writeEndElement()
                        ->asXml(),
                equals('<?xml version="1.0" encoding="UTF-8"?>' . "\n"
                . '<root><foo bar="true"/></root>')
        );
    }

    /**
     * @test
     */
    public function doesNotSupportXmlWriterFeature()
    {
        assertFalse(
                $this->writer->hasFeature(XmlStreamWriter::FEATURE_IMPORT_WRITER)
        );
    }


    /**
     * @test
     */
    public function importStreamWriter()
    {
        expect(function() {
                $this->writer->importStreamWriter(new LibXmlStreamWriter());
        })->throws(\BadMethodCallException::class);
    }

    /**
     * @test
     */
    public function clearRemovesAllPreviouslyAddedElements()
    {
        assert(
                $this->writer->writeElement('foo')->clear()->asXml(),
                equals('<?xml version="1.0" encoding="UTF-8"?>')
        );
    }

    /**
     * @test
     */
    public function hasDomExportFeature()
    {
        assertTrue($this->writer->hasFeature(XmlStreamWriter::FEATURE_AS_DOM));
    }

    /**
     * @test
     */
    public function exportAsDom()
    {
        $dom = $this->writer->writeElement('root', ['foo' => 'bar'])
                            ->asDom();
        assert($dom, isInstanceOf(\DOMDocument::class));
        assert(
                $dom->saveXML(),
                equals('<?xml version="1.0" encoding="UTF-8"?>' . "\n"
                . '<root foo="bar"/>' . "\n")
        );
    }

    /**
     * @test
     */
    public function isFinishedAfterStart()
    {
        assertTrue($this->writer->isFinished());
    }

    /**
     * @test
     */
    public function isNotFinishedAfterElementStarted()
    {
        assertFalse($this->writer->writeStartElement('root')->isFinished());
    }

    /**
     * @test
     */
    public function isNotFinishedAfterLastElementClosed()
    {
        assertTrue(
                $this->writer->writeStartElement('root')
                        ->writeEndElement()
                        ->isFinished()
        );
    }
}
