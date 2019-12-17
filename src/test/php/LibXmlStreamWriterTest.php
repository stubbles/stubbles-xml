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
 * Test for stubbles\xml\LibXmlStreamWriter.
 *
 * @group  xml
 * @group  xml_core
 */
class LibXmlStreamWriterTest extends TestCase
{
    /**
     * @var  LibXmlStreamWriter
     */
    protected $writer;

    protected function setUp(): void
    {
        $this->writer = new LibXmlStreamWriter();
    }

    /**
     * @test
     */
    public function emptyDocumentContainsXmlHeader(): void
    {
        assertThat(
                $this->writer->asXml(),
                equals('<?xml version="1.0" encoding="UTF-8"?>')
        );
    }

    /**
     * @test
     */
    public function canCreateDocumentWithOtherXmlVersion(): void
    {
        $writer = new LibXmlStreamWriter('1.1');
        assertThat(
                $writer->asXml(),
                equals('<?xml version="1.1" encoding="UTF-8"?>')
        );
    }

    /**
     * @test
     */
    public function canCreateDocumentWithIso(): void
    {
        $writer = new LibXmlStreamWriter('1.0', 'ISO-8859-1');
        assertThat(
                $writer->asXml(),
                equals('<?xml version="1.0" encoding="ISO-8859-1"?>')
        );
    }

    /**
     * @test
     */
    public function canCreateDocumentWithOtherVersionAndIso(): void
    {
        $writer = new LibXmlStreamWriter('1.1', 'ISO-8859-1');
        assertThat(
                $writer->asXml(),
                equals('<?xml version="1.1" encoding="ISO-8859-1"?>')
        );
    }

    /**
     * @test
     */
    public function hasVersion1_0ByDefault(): void
    {
        assertThat($this->writer->version(), equals('1.0'));
    }

    /**
     * @test
     */
    public function hasUtf8EncodingByDefault(): void
    {
        assertThat($this->writer->encoding(), equals('UTF-8'));
    }

    /**
     * @test
     */
    public function reportsOtherVersion(): void
    {
        $writer = new LibXmlStreamWriter('1.1', 'ISO-8859-1');
        assertThat($writer->version(), equals('1.1'));
    }

    /**
     * @test
     */
    public function reportsOtherEncoding(): void
    {
        $writer = new LibXmlStreamWriter('1.1', 'ISO-8859-1');
        assertThat($writer->encoding(), equals('ISO-8859-1'));
    }

    /**
     * @test
     */
    public function canWriteNestedElements(): void
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
    public function writeEndElementBeforeStartElementThrowsLogicException(): void
    {
        expect(function() { $this->writer->writeEndElement(); })
                ->throws(\LogicException::class)
                ->withMessage('Can not write end elements, no element open.');
    }

    /**
     * @test
     */
    public function writeElement(): void
    {
        assertThat(
                $this->writer->writeElement('foo', ['att' => 'value'], 'content')
                        ->asXml(),
                equals('<?xml version="1.0" encoding="UTF-8"?>' . "\n"#
                 . '<foo att="value">content</foo>')
        );
    }

    /**
     * @test
     */
    public function writeElementWithGermanUmlautsUtf8(): void
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
    public function writeAttributesAddsAtributesToCurrentElement(): void
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
    public function writeAttributesWithGermanUmlautsUtf8(): void
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
    public function writeTextAddsTextIntoCurrentElement(): void
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
    public function writeTextWithGermanUmlautsInUtf8(): void
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
    public function writeCDataAddsCdata(): void
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
    public function writeCDataWithGermanUmlautsInUtf8(): void
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
    public function writeCommentAddsComment(): void
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
    public function commentWithGermanUmlautsInUtf8(): void
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
    public function processingInstructionCanBeAdded(): void
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
    public function xmlFragmentCanBeAdded(): void
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
    public function doesNotSupportXmlWriterFeature(): void
    {
        assertFalse(
                $this->writer->hasFeature(XmlStreamWriter::FEATURE_IMPORT_WRITER)
        );
    }


    /**
     * @test
     */
    public function importStreamWriter(): void
    {
        expect(function() {
                $this->writer->importStreamWriter(new LibXmlStreamWriter());
        })->throws(\BadMethodCallException::class);
    }

    /**
     * @test
     */
    public function clearRemovesAllPreviouslyAddedElements(): void
    {
        assertThat(
                $this->writer->writeElement('foo')->clear()->asXml(),
                equals('<?xml version="1.0" encoding="UTF-8"?>')
        );
    }

    /**
     * @test
     */
    public function hasDomExportFeature(): void
    {
        assertTrue($this->writer->hasFeature(XmlStreamWriter::FEATURE_AS_DOM));
    }

    /**
     * @test
     */
    public function exportAsDom(): void
    {
        $dom = $this->writer->writeElement('root', ['foo' => 'bar'])
                            ->asDom();
        assertThat($dom, isInstanceOf(\DOMDocument::class));
        assertThat(
                $dom->saveXML(),
                equals('<?xml version="1.0" encoding="UTF-8"?>' . "\n"
                . '<root foo="bar"/>' . "\n")
        );
    }

    /**
     * @test
     */
    public function isFinishedAfterStart(): void
    {
        assertTrue($this->writer->isFinished());
    }

    /**
     * @test
     */
    public function isNotFinishedAfterElementStarted(): void
    {
        assertFalse($this->writer->writeStartElement('root')->isFinished());
    }

    /**
     * @test
     */
    public function isNotFinishedAfterLastElementClosed(): void
    {
        assertTrue(
                $this->writer->writeStartElement('root')
                        ->writeEndElement()
                        ->isFinished()
        );
    }
}
