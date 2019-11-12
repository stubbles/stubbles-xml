<?php
declare(strict_types=1);
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace stubbles\xml;
use PHPUnit\Framework\TestCase;

use function bovigo\assert\assertThat;
use function bovigo\assert\assertFalse;
use function bovigo\assert\assertTrue;
use function bovigo\assert\expect;
use function bovigo\assert\predicate\equals;
use function bovigo\assert\predicate\isInstanceOf;
/**
 * Test for stubbles\xml\DomXmlStreamWriter.
 *
 * @group  xml
 * @group  xml_core
 */
class DomXmlStreamWriterTest extends TestCase
{
    /**
     * instance to test
     *
     * @type  DomXmlStreamWriter
     */
    protected $writer;

    protected function setUp(): void
    {
        $this->writer = new DomXmlStreamWriter();
    }

    /**
     * @test
     */
    public function emptyDocumentContainsXmlHeader()
    {
        assertThat(
                $this->writer->asXml(),
                equals('<?xml version="1.0" encoding="UTF-8"?>')
        );
    }

    /**
     * @test
     */
    public function canCreateDocumentWithOtherXmlVersion()
    {
        $writer = new DomXmlStreamWriter('1.1');
        assertThat(
                $writer->asXml(),
                equals('<?xml version="1.1" encoding="UTF-8"?>')
        );
    }

    /**
     * @test
     */
    public function canCreateDocumentWithIso()
    {
        $writer = new DomXmlStreamWriter('1.0', 'ISO-8859-1');
        assertThat(
                $writer->asXml(),
                equals('<?xml version="1.0" encoding="ISO-8859-1"?>')
        );
    }

    /**
     * @test
     */
    public function canCreateDocumentWithOtherVersionAndIso()
    {
        $writer = new DomXmlStreamWriter('1.1', 'ISO-8859-1');
        assertThat(
                $writer->asXml(),
                equals('<?xml version="1.1" encoding="ISO-8859-1"?>')
        );
    }

    /**
     * @test
     */
    public function hasVersion1_0ByDefault()
    {
        assertThat($this->writer->version(), equals('1.0'));
    }

    /**
     * @test
     */
    public function hasUtf8EncodingByDefault()
    {
        assertThat($this->writer->encoding(), equals('UTF-8'));
    }

    /**
     * @test
     */
    public function reportsOtherVersion()
    {
        $writer = new DomXmlStreamWriter('1.1', 'ISO-8859-1');
        assertThat($writer->version(), equals('1.1'));
    }

    /**
     * @test
     */
    public function reportsOtherEncoding()
    {
        $writer = new DomXmlStreamWriter('1.1', 'ISO-8859-1');
        assertThat($writer->encoding(), equals('ISO-8859-1'));
    }

    /**
     * @test
     */
    public function canWriteNestedElements()
    {
        assertThat(
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
    public function canWriteNestedCompleteElements()
    {
        assertThat(
                $this->writer->writeStartElement('root')
                        ->writeStartElement('foo')
                        ->writeEndElement()
                        ->writeElement('bar', ['baz' => 'blub'], 'content')
                        ->writeEndElement()
                        ->asXml(),
                equals('<?xml version="1.0" encoding="UTF-8"?>' . "\n"
                . '<root><foo/><bar baz="blub">content</bar></root>')
        );
    }

    /**
     * @test
     */
    public function writeEndElementBeforeStartElementThrowsLogicException()
    {
        expect(function() { $this->writer->writeEndElement(); })
                ->throws(\LogicException::class)
                ->withMessage('Can not write end elements, no element open.');
    }

    /**
     * @test
     */
    public function writeElement()
    {
        assertThat(
                $this->writer->writeElement('foo', ['att' => 'value'], 'content')
                        ->asXml(),
                equals('<?xml version="1.0" encoding="UTF-8"?>' . "\n"
                . '<foo att="value">content</foo>')
        );
    }

    /**
     * @test
     */
    public function writeElementWithGermanUmlautsInNonUtf8WillEncodeValue()
    {
        assertThat(
                $this->writer->writeElement('foo', ['att' => utf8_decode('hääää')], 'content')
                        ->asXml(),
                equals('<?xml version="1.0" encoding="UTF-8"?>' . "\n"
                . '<foo att="hääää">content</foo>')
        );
    }

    /**
     * @test
     */
    public function writeElementWithGermanUmlautsUtf8()
    {
        assertThat(
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
        assertThat(
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
    public function writeAttributesWithGermanUmlautsInNonUtf8WillEncodeValue()
    {
        assertThat(
                $this->writer->writeStartElement('root')
                        ->writeStartElement('foo')
                        ->writeAttribute('bar', utf8_decode('hääää'))
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
    public function writeAttributesWithGermanUmlautsUtf8()
    {
        assertThat(
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
        assertThat(
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
    public function writeTextWithGermanUmlautsInNonUtf8WillEncodeText()
    {
        assertThat(
                $this->writer->writeStartElement('root')
                        ->writeText(utf8_decode('This is text containing äöü.'))
                        ->writeEndElement()
                        ->asXml(),
                equals('<?xml version="1.0" encoding="UTF-8"?>' . "\n"
                . '<root>This is text containing äöü.</root>')
        );
    }

    /**
     * @test
     */
    public function writeTextWithGermanUmlautsInUtf8()
    {
        assertThat(
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
        assertThat(
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
    public function writeCDataWithGermanUmlautsInNonUtf8WillEncodeCData()
    {
        assertThat(
                $this->writer->writeStartElement('root')
                        ->writeCData(utf8_decode('This is text containing äöü.'))
                        ->writeEndElement()
                        ->asXml(),
                equals('<?xml version="1.0" encoding="UTF-8"?>' . "\n"
                . '<root><![CDATA[This is text containing äöü.]]></root>')
        );
    }

    /**
     * @test
     */
    public function writeCDataWithGermanUmlautsInUtf8()
    {
        assertThat(
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
        assertThat(
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
    public function writeCommentWithGermanUmlautsInNonUtf8WillEncodeComment()
    {
        assertThat(
                $this->writer->writeStartElement('root')
                        ->writeComment(utf8_decode('This is a comment containing äöü.'))
                        ->writeEndElement()
                        ->asXml(),
                equals('<?xml version="1.0" encoding="UTF-8"?>' . "\n"
                . '<root><!--This is a comment containing äöü.--></root>')
        );
    }

    /**
     * @test
     */
    public function commentWithGermanUmlautsInUtf8()
    {
        assertThat(
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
        assertThat(
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
    public function writingInvalidXmlFragmentThrowsXmlException()
    {
        expect(function() {
                $this->writer->writeStartElement('root')
                        ->writeXmlFragment('<foo>');
        })->throws(XmlException::class);
    }

    /**
     * @test
     */
    public function xmlFragmentCanBeAdded()
    {
        assertThat(
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
    public function hasImportXmlWriterFeature()
    {
        assertTrue(
                $this->writer->hasFeature(XmlStreamWriter::FEATURE_IMPORT_WRITER)
        );
    }

    /**
     * @test
     */
    public function importStreamWriterAddsElementsFromImportedStreamWriter()
    {
        $otherWriter = new DomXmlStreamWriter();
        $otherWriter->writeStartElement('foo')
                ->writeStartElement('bar')
                ->writeEndElement()
                ->writeEndElement();
        assertThat(
                $this->writer->writeStartElement('root')
                        ->importStreamWriter($otherWriter)
                        ->writeEndElement()
                        ->asXml(),
                equals('<?xml version="1.0" encoding="UTF-8"?>' . "\n"
                . '<root><foo><bar/></foo></root>')
        );
    }

    /**
     * @test
     */
    public function clearRemovesAllPreviouslyAddedElements()
    {
        assertThat(
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
        $dom = $this->writer->writeElement('root', ['foo' => 'bar'])->asDom();
        assertThat($dom, isInstanceOf(\DOMDocument::class));
        assertThat(
                $dom->saveXML(),
                equals('<?xml version="1.0" encoding="UTF-8"?>' . "\n". '<root foo="bar"/>' . "\n")
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
