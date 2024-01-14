<?php
declare(strict_types=1);
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace stubbles\xml;

use DOMDocument;
use LogicException;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

use function bovigo\assert\assertThat;
use function bovigo\assert\assertFalse;
use function bovigo\assert\assertTrue;
use function bovigo\assert\expect;
use function bovigo\assert\predicate\equals;
use function bovigo\assert\predicate\isInstanceOf;
/**
 * Test for stubbles\xml\DomXmlStreamWriter.
 */
#[Group('xml')]
#[Group('xml_core')]
class DomXmlStreamWriterTest extends TestCase
{
    private DomXmlStreamWriter $writer;

    protected function setUp(): void
    {
        $this->writer = new DomXmlStreamWriter();
    }

    #[Test]
    public function emptyDocumentContainsXmlHeader(): void
    {
        assertThat(
            $this->writer->asXml(),
            equals('<?xml version="1.0" encoding="UTF-8"?>')
        );
    }

    #[Test]
    public function canCreateDocumentWithOtherXmlVersion(): void
    {
        $writer = new DomXmlStreamWriter('1.1');
        assertThat(
            $writer->asXml(),
            equals('<?xml version="1.1" encoding="UTF-8"?>')
        );
    }

    #[Test]
    public function canCreateDocumentWithIso(): void
    {
        $writer = new DomXmlStreamWriter('1.0', 'ISO-8859-1');
        assertThat(
            $writer->asXml(),
            equals('<?xml version="1.0" encoding="ISO-8859-1"?>')
        );
    }

    #[Test]
    public function canCreateDocumentWithOtherVersionAndIso(): void
    {
        $writer = new DomXmlStreamWriter('1.1', 'ISO-8859-1');
        assertThat(
            $writer->asXml(),
            equals('<?xml version="1.1" encoding="ISO-8859-1"?>')
        );
    }

    #[Test]
    public function hasVersion1_0ByDefault(): void
    {
        assertThat($this->writer->version(), equals('1.0'));
    }

    #[Test]
    public function hasUtf8EncodingByDefault(): void
    {
        assertThat($this->writer->encoding(), equals('UTF-8'));
    }

    #[Test]
    public function reportsOtherVersion(): void
    {
        $writer = new DomXmlStreamWriter('1.1', 'ISO-8859-1');
        assertThat($writer->version(), equals('1.1'));
    }

    #[Test]
    public function reportsOtherEncoding(): void
    {
        $writer = new DomXmlStreamWriter('1.1', 'ISO-8859-1');
        assertThat($writer->encoding(), equals('ISO-8859-1'));
    }

    #[Test]
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
            equals(
                '<?xml version="1.0" encoding="UTF-8"?>'
                . "\n<root><foo/><bar/></root>"
            )
        );
    }

    #[Test]
    public function canWriteNestedCompleteElements(): void
    {
        assertThat(
            $this->writer->writeStartElement('root')
                ->writeStartElement('foo')
                ->writeEndElement()
                ->writeElement('bar', ['baz' => 'blub'], 'content')
                ->writeEndElement()
                ->asXml(),
            equals(
                '<?xml version="1.0" encoding="UTF-8"?>' . "\n"
                . '<root><foo/><bar baz="blub">content</bar></root>'
            )
        );
    }

    #[Test]
    public function writeEndElementBeforeStartElementThrowsLogicException(): void
    {
        expect(function() { $this->writer->writeEndElement(); })
            ->throws(LogicException::class)
            ->withMessage('Can not write end elements, no element open.');
    }

    #[Test]
    public function writeElement(): void
    {
        assertThat(
            $this->writer->writeElement('foo', ['att' => 'value'], 'content')
                ->asXml(),
            equals(
                '<?xml version="1.0" encoding="UTF-8"?>' . "\n"
                . '<foo att="value">content</foo>'
            )
        );
    }

    #[Test]
    public function writeElementWithGermanUmlautsInNonUtf8WillEncodeValue(): void
    {
        assertThat(
            $this->writer->writeElement(
                'foo',
                ['att' => mb_convert_encoding('hääää', 'ISO-8859-1')],
                'content'
            )->asXml(),
            equals(
                '<?xml version="1.0" encoding="UTF-8"?>' . "\n"
                . '<foo att="hääää">content</foo>'
            )
        );
    }

    #[Test]
    public function writeElementWithGermanUmlautsUtf8(): void
    {
        assertThat(
            $this->writer->writeElement('foo', ['att' => 'hääää'], 'content')
                ->asXml(),
            equals(
                '<?xml version="1.0" encoding="UTF-8"?>' . "\n"
                . '<foo att="hääää">content</foo>'
            )
        );
    }

    #[Test]
    public function writeAttributesAddsAtributesToCurrentElement(): void
    {
        assertThat(
            $this->writer->writeStartElement('root')
                ->writeStartElement('foo')
                ->writeAttribute('bar', '42')
                ->writeEndElement()
                ->writeEndElement()
                ->asXml(),
            equals(
                '<?xml version="1.0" encoding="UTF-8"?>' . "\n"
                . '<root><foo bar="42"/></root>'
            )
        );
    }

    #[Test]
    public function writeAttributesWithGermanUmlautsInNonUtf8WillEncodeValue(): void
    {
        assertThat(
            $this->writer->writeStartElement('root')
                ->writeStartElement('foo')
                ->writeAttribute('bar', mb_convert_encoding('hääää', 'ISO-8859-1', 'UTF-8'))
                ->writeEndElement()
                ->writeEndElement()
                ->asXml(),
            equals(
                '<?xml version="1.0" encoding="UTF-8"?>' . "\n"
                . '<root><foo bar="hääää"/></root>'
            )
        );
    }

    #[Test]
    public function writeAttributesWithGermanUmlautsUtf8(): void
    {
        assertThat(
            $this->writer->writeStartElement('root')
                ->writeStartElement('foo')
                ->writeAttribute('bar', 'hääää')
                ->writeEndElement()
                ->writeEndElement()
                ->asXml(),
            equals(
                '<?xml version="1.0" encoding="UTF-8"?>' . "\n"
                . '<root><foo bar="hääää"/></root>'
            )
        );
    }

    #[Test]
    public function writeTextAddsTextIntoCurrentElement(): void
    {
        assertThat(
            $this->writer->writeStartElement('root')
                ->writeText('This is text.')
                ->writeEndElement()
                ->asXml(),
            equals(
                '<?xml version="1.0" encoding="UTF-8"?>' . "\n"
                . '<root>This is text.</root>'
            )
        );
    }

    private function textWithGermanUmlauts(
        string $text = 'This is text containing äöü.'
    ): string {
        return mb_convert_encoding(
            $text,
            'ISO-8859-1',
            'UTF-8'
        );
    }

    #[Test]
    public function writeTextWithGermanUmlautsInNonUtf8WillEncodeText(): void
    {
        assertThat(
            $this->writer->writeStartElement('root')
                ->writeText($this->textWithGermanUmlauts())
                ->writeEndElement()
                ->asXml(),
            equals(
                '<?xml version="1.0" encoding="UTF-8"?>' . "\n"
                . '<root>This is text containing äöü.</root>'
            )
        );
    }

    #[Test]
    public function writeTextWithGermanUmlautsInUtf8(): void
    {
        assertThat(
            $this->writer->writeStartElement('root')
                ->writeText('This is text containing äöü.')
                ->writeEndElement()
                ->asXml(),
            equals(
                '<?xml version="1.0" encoding="UTF-8"?>' . "\n"
                . '<root>This is text containing äöü.</root>'
            )
        );
    }

    #[Test]
    public function writeCDataAddsCdata(): void
    {
        assertThat(
            $this->writer->writeStartElement('root')
                ->writeCData('This is text.')
                ->writeEndElement()
                ->asXml(),
            equals(
                '<?xml version="1.0" encoding="UTF-8"?>' . "\n"
                . '<root><![CDATA[This is text.]]></root>'
            )
        );
    }

    #[Test]
    public function writeCDataWithGermanUmlautsInNonUtf8WillEncodeCData(): void
    {
        assertThat(
            $this->writer->writeStartElement('root')
                ->writeCData($this->textWithGermanUmlauts())
                ->writeEndElement()
                ->asXml(),
            equals(
                '<?xml version="1.0" encoding="UTF-8"?>' . "\n"
                . '<root><![CDATA[This is text containing äöü.]]></root>'
            )
        );
    }

    #[Test]
    public function writeCDataWithGermanUmlautsInUtf8(): void
    {
        assertThat(
            $this->writer->writeStartElement('root')
                ->writeCData('This is text containing äöü.')
                ->writeEndElement()
                ->asXml(),
            equals(
                '<?xml version="1.0" encoding="UTF-8"?>' . "\n"
                . '<root><![CDATA[This is text containing äöü.]]></root>'
            )
        );
    }

    #[Test]
    public function writeCommentAddsComment(): void
    {
        assertThat(
            $this->writer->writeStartElement('root')
                ->writeComment('This is a comment.')
                ->writeEndElement()
                ->asXml(),
            equals(
                '<?xml version="1.0" encoding="UTF-8"?>' . "\n"
                . '<root><!--This is a comment.--></root>'
            )
        );
    }

    #[Test]
    public function writeCommentWithGermanUmlautsInNonUtf8WillEncodeComment(): void
    {
        assertThat(
            $this->writer->writeStartElement('root')
                ->writeComment(
                    $this->textWithGermanUmlauts('This is a comment containing äöü.')
                )->writeEndElement()
                ->asXml(),
            equals(
                '<?xml version="1.0" encoding="UTF-8"?>' . "\n"
                . '<root><!--This is a comment containing äöü.--></root>'
            )
        );
    }

    #[Test]
    public function commentWithGermanUmlautsInUtf8(): void
    {
        assertThat(
            $this->writer->writeStartElement('root')
                ->writeComment('This is a comment containing äöü.')
                ->writeEndElement()
                ->asXml(),
            equals(
                '<?xml version="1.0" encoding="UTF-8"?>' . "\n"
                . '<root><!--This is a comment containing äöü.--></root>'
            )
        );
    }

    #[Test]
    public function processingInstructionCanBeAdded(): void
    {
        assertThat(
            $this->writer->writeStartElement('root')
                ->writeProcessingInstruction('php', 'phpinfo();')
                ->writeEndElement()
                ->asXml(),
            equals(
                '<?xml version="1.0" encoding="UTF-8"?>' . "\n"
                . '<root><?php phpinfo();?></root>'
            )
        );
    }

    #[Test]
    public function writingInvalidXmlFragmentThrowsXmlException(): void
    {
        expect(function() {
            $this->writer->writeStartElement('root')
                ->writeXmlFragment('<foo>');
        })->throws(XmlException::class);
    }

    #[Test]
    public function xmlFragmentCanBeAdded(): void
    {
        assertThat(
            $this->writer->writeStartElement('root')
                ->writeXmlFragment('<foo bar="true"/>')
                ->writeEndElement()
                ->asXml(),
            equals(
                '<?xml version="1.0" encoding="UTF-8"?>' . "\n"
                . '<root><foo bar="true"/></root>'
            )
        );
    }

    #[Test]
    public function hasImportXmlWriterFeature(): void
    {
        assertTrue(
            $this->writer->hasFeature(XmlStreamWriter::FEATURE_IMPORT_WRITER)
        );
    }

    #[Test]
    public function importStreamWriterAddsElementsFromImportedStreamWriter(): void
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
            equals(
                '<?xml version="1.0" encoding="UTF-8"?>' . "\n"
                . '<root><foo><bar/></foo></root>'
            )
        );
    }

    #[Test]
    public function clearRemovesAllPreviouslyAddedElements(): void
    {
        assertThat(
            $this->writer->writeElement('foo')->clear()->asXml(),
            equals('<?xml version="1.0" encoding="UTF-8"?>')
        );
    }

    #[Test]
    public function hasDomExportFeature(): void
    {
        assertTrue($this->writer->hasFeature(XmlStreamWriter::FEATURE_AS_DOM));
    }

    #[Test]
    public function exportAsDom(): void
    {
        $dom = $this->writer->writeElement('root', ['foo' => 'bar'])->asDom();
        assertThat($dom, isInstanceOf(DOMDocument::class));
        assertThat(
            $dom->saveXML(),
            equals(
                '<?xml version="1.0" encoding="UTF-8"?>' . "\n". '<root foo="bar"/>' . "\n"
            )
        );
    }

    #[Test]
    public function isFinishedAfterStart(): void
    {
        assertTrue($this->writer->isFinished());
    }

    #[Test]
    public function isNotFinishedAfterElementStarted(): void
    {
        assertFalse($this->writer->writeStartElement('root')->isFinished());
    }

    #[Test]
    public function isNotFinishedAfterLastElementClosed(): void
    {
        assertTrue(
            $this->writer->writeStartElement('root')
                ->writeEndElement()
                ->isFinished()
        );
    }
}
