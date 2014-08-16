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
        $this->assertEquals('<?xml version="1.0" encoding="UTF-8"?>',
                            $this->writer->asXml()
        );
    }

    /**
     * @test
     */
    public function canCreateDocumentWithOtherXmlVersion()
    {
        $writer = new LibXmlStreamWriter('1.1');
        $this->assertEquals('<?xml version="1.1" encoding="UTF-8"?>',
                            $writer->asXml()
        );
    }

    /**
     * @test
     */
    public function canCreateDocumentWithIso()
    {
        $writer = new LibXmlStreamWriter('1.0', 'ISO-8859-1');
        $this->assertEquals('<?xml version="1.0" encoding="ISO-8859-1"?>',
                            $writer->asXml()
        );
    }

    /**
     * @test
     */
    public function canCreateDocumentWithOtherVersionAndIso()
    {
        $writer = new LibXmlStreamWriter('1.1', 'ISO-8859-1');
        $this->assertEquals('<?xml version="1.1" encoding="ISO-8859-1"?>',
                            $writer->asXml()
        );
    }

    /**
     * @test
     */
    public function hasVersion1_0ByDefault()
    {
        $this->assertEquals('1.0', $this->writer->getVersion());
    }

    /**
     * @test
     */
    public function hasUtf8EncodingByDefault()
    {
        $this->assertEquals('UTF-8', $this->writer->getEncoding());
    }

    /**
     * @test
     */
    public function reportsOtherVersion()
    {
        $writer = new LibXmlStreamWriter('1.1', 'ISO-8859-1');
        $this->assertEquals('1.1', $writer->getVersion());
    }

    /**
     * @test
     */
    public function reportsOtherEncoding()
    {
        $writer = new LibXmlStreamWriter('1.1', 'ISO-8859-1');
        $this->assertEquals('ISO-8859-1', $writer->getEncoding());
    }

    /**
     * @test
     */
    public function canWriteNestedElements()
    {
        $this->assertEquals('<?xml version="1.0" encoding="UTF-8"?>' . "\n<root><foo/><bar/></root>" ,
                            $this->writer->writeStartElement('root')
                                         ->writeStartElement('foo')
                                         ->writeEndElement()
                                         ->writeStartElement('bar')
                                         ->writeEndElement()
                                         ->writeEndElement()
                                         ->asXml()
        );
    }

    /**
     * @test
     */
    public function writeElement()
    {
        $this->assertEquals('<?xml version="1.0" encoding="UTF-8"?>' . "\n" . '<foo att="value">content</foo>',
                            $this->writer->writeElement('foo', ['att' => 'value'], 'content')
                                         ->asXml()
        );
    }

    /**
     * @test
     */
    public function writeElementWithGermanUmlautsUtf8()
    {
        $this->assertEquals('<?xml version="1.0" encoding="UTF-8"?>' . "\n" . '<foo att="hääää">content</foo>',
                            $this->writer->writeElement('foo', ['att' => 'hääää'], 'content')
                                         ->asXml()
        );
    }

    /**
     * @test
     */
    public function writeAttributesAddsAtributesToCurrentElement()
    {
        $this->assertEquals('<?xml version="1.0" encoding="UTF-8"?>' . "\n". '<root><foo bar="42"/></root>',
                            $this->writer->writeStartElement('root')
                                         ->writeStartElement('foo')
                                         ->writeAttribute('bar', '42')
                                         ->writeEndElement()
                                         ->writeEndElement()
                                         ->asXml()
        );
    }

    /**
     * @test
     */
    public function writeAttributesWithGermanUmlautsUtf8()
    {
        $this->assertEquals('<?xml version="1.0" encoding="UTF-8"?>' . "\n". '<root><foo bar="hääää"/></root>',
                            $this->writer->writeStartElement('root')
                                         ->writeStartElement('foo')
                                         ->writeAttribute('bar', 'hääää')
                                         ->writeEndElement()
                                         ->writeEndElement()
                                         ->asXml()
        );
    }

    /**
     * @test
     */
    public function writeTextAddsTextIntoCurrentElement()
    {
        $this->assertEquals('<?xml version="1.0" encoding="UTF-8"?>' . "\n". '<root>This is text.</root>',
                            $this->writer->writeStartElement('root')
                                         ->writeText('This is text.')
                                         ->writeEndElement()
                                         ->asXml()
        );
    }

    /**
     * @test
     */
    public function writeTextWithGermanUmlautsInUtf8()
    {
        $this->assertEquals('<?xml version="1.0" encoding="UTF-8"?>' . "\n". '<root>This is text containing äöü.</root>',
                            $this->writer->writeStartElement('root')
                                         ->writeText('This is text containing äöü.')
                                         ->writeEndElement()
                                         ->asXml()
        );
    }

    /**
     * @test
     */
    public function writeCDataAddsCdata()
    {
        $this->assertEquals('<?xml version="1.0" encoding="UTF-8"?>' . "\n". '<root><![CDATA[This is text.]]></root>',
                            $this->writer->writeStartElement('root')
                                         ->writeCData('This is text.')
                                         ->writeEndElement()
                                         ->asXml()
        );
    }

    /**
     * @test
     */
    public function writeCDataWithGermanUmlautsInUtf8()
    {
        $this->assertEquals('<?xml version="1.0" encoding="UTF-8"?>' . "\n". '<root><![CDATA[This is text containing äöü.]]></root>',
                            $this->writer->writeStartElement('root')
                                         ->writeCData('This is text containing äöü.')
                                         ->writeEndElement()
                                         ->asXml()
        );
    }

    /**
     * @test
     */
    public function writeCommentAddsComment()
    {
        $this->assertEquals('<?xml version="1.0" encoding="UTF-8"?>' . "\n". '<root><!--This is a comment.--></root>',
                            $this->writer->writeStartElement('root')
                                         ->writeComment('This is a comment.')
                                         ->writeEndElement()
                                         ->asXml()
        );
    }

    /**
     * @test
     */
    public function commentWithGermanUmlautsInUtf8()
    {
        $this->assertEquals('<?xml version="1.0" encoding="UTF-8"?>' . "\n". '<root><!--This is a comment containing äöü.--></root>',
                            $this->writer->writeStartElement('root')
                                         ->writeComment('This is a comment containing äöü.')
                                         ->writeEndElement()
                                         ->asXml()
        );
    }

    /**
     * @test
     */
    public function processingInstructionCanBeAdded()
    {
        $this->assertEquals('<?xml version="1.0" encoding="UTF-8"?>' . "\n". '<root><?php phpinfo();?></root>',
                            $this->writer->writeStartElement('root')
                                         ->writeProcessingInstruction('php', 'phpinfo();')
                                         ->writeEndElement()
                                         ->asXml()
        );
    }

    /**
     * @test
     */
    public function xmlFragmentCanBeAdded()
    {
        $this->assertEquals('<?xml version="1.0" encoding="UTF-8"?>' . "\n". '<root><foo bar="true"/></root>',
                            $this->writer->writeStartElement('root')
                                         ->writeXmlFragment('<foo bar="true"/>')
                                         ->writeEndElement()
                                         ->asXml()
        );
    }

    /**
     * @test
     */
    public function doesNotSupportXmlWriterFeature()
    {
        $this->assertFalse($this->writer->hasFeature(XmlStreamWriter::FEATURE_IMPORT_WRITER));
    }


    /**
     * @test
     * @expectedException  BadMethodCallException
     */
    public function importStreamWriter()
    {
        $this->writer->importStreamWriter(new LibXmlStreamWriter());
    }

    /**
     * @test
     */
    public function clearRemovesAllPreviouslyAddedElements()
    {
        $this->assertEquals('<?xml version="1.0" encoding="UTF-8"?>',
                            $this->writer->writeElement('foo')
                                         ->clear()
                                         ->asXml()
        );
    }

    /**
     * @test
     */
    public function hasDomExportFeature()
    {
        $this->assertTrue($this->writer->hasFeature(XmlStreamWriter::FEATURE_AS_DOM));
    }

    /**
     * @test
     */
    public function exportAsDom()
    {
        $dom = $this->writer->writeElement('root', ['foo' => 'bar'])
                            ->asDom();
        $this->assertInstanceOf('\\DOMDocument', $dom);
        $this->assertEquals('<?xml version="1.0" encoding="UTF-8"?>' . "\n". '<root foo="bar"/>' . "\n",
                            $dom->saveXML()
        );
    }

    /**
     * @test
     */
    public function isFinishedAfterStart()
    {
        $this->assertTrue($this->writer->isFinished());
    }

    /**
     * @test
     */
    public function isNotFinishedAfterElementStarted()
    {
        $this->assertFalse($this->writer->writeStartElement('root')->isFinished());
    }

    /**
     * @test
     */
    public function isNotFinishedAfterLastElementClosed()
    {
        $this->assertTrue($this->writer->writeStartElement('root')
                                       ->writeEndElement()
                                       ->isFinished()
        );
    }
}
