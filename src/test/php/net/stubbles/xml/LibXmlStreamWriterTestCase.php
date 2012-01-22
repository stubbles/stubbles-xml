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
 * Test for net\stubbles\xml\LibXmlStreamWriter.
 *
 * @group  xml
 * @group  xml_core
 */
class LibXmlStreamWriterTestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * Test the creation of an empty document
     *
     * @test
     */
    public function emptyDocument()
    {
        $writer = new LibXmlStreamWriter();
        $this->assertEquals('<?xml version="1.0" encoding="UTF-8"?>', $writer->asXml());
        $this->assertEquals('1.0', $writer->getVersion());
        $this->assertEquals('UTF-8', $writer->getEncoding());
        $writer = new LibXmlStreamWriter('1.1', 'ISO-8859-1');
        $this->assertEquals('1.1', $writer->getVersion());
        $this->assertEquals('ISO-8859-1', $writer->getEncoding());
    }

    /**
     * Test creating a document with several tags
     *
     * @test
     */
    public function tags()
    {
        $writer = new LibXmlStreamWriter();
        $writer->writeStartElement('root');
        $writer->writeStartElement('foo');
        $writer->writeEndElement();
        $writer->writeStartElement('bar');
        $writer->writeEndElement();
        $writer->writeEndElement();

        $this->assertEquals('<?xml version="1.0" encoding="UTF-8"?>' . "\n<root><foo/><bar/></root>" , $writer->asXml());
    }

    /**
     * Test creating a document with several tags
     *
     * @test
     */
    public function fullElement()
    {
        $writer = new LibXmlStreamWriter();
        $writer->writeElement('foo', array('att' => 'value'), 'content');

        $this->assertEquals('<?xml version="1.0" encoding="UTF-8"?>' . "\n" . '<foo att="value">content</foo>', $writer->asXml());
    }

    /**
     * Test creating a document with attributes
     *
     * @test
     */
    public function attributes()
    {
        $writer = new LibXmlStreamWriter();
        $writer->writeStartElement('root');
        $writer->writeStartElement('foo');
        $writer->writeAttribute('bar', '42');
        $writer->writeEndElement();
        $writer->writeEndElement();

        $this->assertEquals('<?xml version="1.0" encoding="UTF-8"?>' . "\n". '<root><foo bar="42"/></root>' , $writer->asXml());
    }

    /**
     * Test creating a document with a text node
     *
     * @test
     */
    public function text()
    {
        $writer = new LibXmlStreamWriter();
        $writer->writeStartElement('root');
        $writer->writeText('This is text.');
        $writer->writeEndElement();

        $this->assertEquals('<?xml version="1.0" encoding="UTF-8"?>' . "\n". '<root>This is text.</root>' , $writer->asXml());
    }

    /**
     * Test creating a document with character data
     *
     * @test
     */
    public function cData()
    {
        $writer = new LibXmlStreamWriter();
        $writer->writeStartElement('root');
        $writer->writeCData('This is text.');
        $writer->writeEndElement();

        $this->assertEquals('<?xml version="1.0" encoding="UTF-8"?>' . "\n". '<root><![CDATA[This is text.]]></root>', $writer->asXml());
    }

    /**
     * Test creating a document with a comment
     *
     * @test
     */
    public function comment()
    {
        $writer = new LibXmlStreamWriter();
        $writer->writeStartElement('root');
        $writer->writeComment('This is a comment.');
        $writer->writeEndElement();

        $this->assertEquals('<?xml version="1.0" encoding="UTF-8"?>' . "\n". '<root><!--This is a comment.--></root>' , $writer->asXml());
    }

    /**
     * Test creating a document with a processing instruction
     *
     * @test
     */
    public function processingInstruction()
    {
        $writer = new LibXmlStreamWriter();
        $writer->writeStartElement('root');
        $writer->writeProcessingInstruction('php', 'phpinfo();');
        $writer->writeEndElement();

        $this->assertEquals('<?xml version="1.0" encoding="UTF-8"?>' . "\n". '<root><?php phpinfo();?></root>' , $writer->asXml());
    }

    /**
     * Test creating a document an XML fragment
     *
     * @test
     */
    public function xmlFragment()
    {
        $writer = new LibXmlStreamWriter();
        $writer->writeStartElement('root');
        $writer->writeXmlFragment('<foo bar="true"/>');
        $writer->writeEndElement();

        $this->assertEquals('<?xml version="1.0" encoding="UTF-8"?>' . "\n". '<root><foo bar="true"/></root>' , $writer->asXml());
    }

    /**
     * Test importing a stream writer
     *
     * @test
     * @expectedException  net\stubbles\lang\exception\MethodNotSupportedException
     */
    public function importStreamWriter()
    {
        $writer = new LibXmlStreamWriter();
        $writer->writeStartElement('root');

        $writer2 = new LibXmlStreamWriter();
        $writer2->writeStartElement('foo');
        $writer2->writeStartElement('bar');
        $writer2->writeEndElement();
        $writer2->writeEndElement();

        $writer->importStreamWriter($writer2);
    }


    /**
     * Test the clear() method
     *
     * @test
     */
    public function clear()
    {
        $writer = new LibXmlStreamWriter();
        $writer->writeElement('foo');
        $writer->clear();
        $this->assertEquals('<?xml version="1.0" encoding="UTF-8"?>', $writer->asXml());
    }

    /**
     * Test the hasFeature() method
     *
     * @test
     */
    public function features()
    {
        $writer = new LibXmlStreamWriter();
        $this->assertTrue($writer->hasFeature(XmlStreamWriter::FEATURE_AS_DOM));
        $this->assertFalse($writer->hasFeature(XmlStreamWriter::FEATURE_IMPORT_WRITER));
    }

    /**
     * checks if the finished status is reported properly
     *
     * @test
     */
    public function isFinished()
    {
        $writer = new LibXmlStreamWriter();
        $this->assertTrue($writer->isFinished());
        $writer->writeStartElement('root');
        $this->assertFalse($writer->isFinished());
        $writer->writeEndElement();
        $this->assertTrue($writer->isFinished());
    }

    /**
     * @test
     */
    public function exportAsDom()
    {
        $writer = new LibXmlStreamWriter();
        $writer->writeElement('root', array('foo' => 'bar'));
        $dom = $writer->asDom();
        $this->assertInstanceOf('\\DOMDocument', $dom);
        $this->assertEquals('<?xml version="1.0" encoding="UTF-8"?>
<root foo="bar"/>
',
                            $dom->saveXML()
        );
    }
}
?>