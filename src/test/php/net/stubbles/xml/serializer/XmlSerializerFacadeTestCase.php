<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  net\stubbles\xml
 */
namespace net\stubbles\xml\serializer;
/**
 * Test for net\stubbles\xml\serializer\XmlSerializerFacade.
 *
 * @since  1.1.0
 * @group  xml
 * @group  xml_serializer
 */
class XmlSerializerFacadeTestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * instance to test
     *
     * @type  XmlSerializerFacade
     */
    protected $xmlSerializerFacade;
    /**
     * mocked xml serializer
     *
     * @type  \PHPUnit_Framework_MockObject_MockObject
     */
    protected $mockXmlSerializer;
    /**
     * mocked xml stream writer
     *
     * @type  \PHPUnit_Framework_MockObject_MockObject
     */
    protected $mockXmlStreamWriter;

    /**
     * set up test environment
     */
    public function setUp()
    {
        libxml_clear_errors();
        $this->mockXmlSerializer   = $this->getMock('net\\stubbles\\xml\\serializer\\XMLSerializer', array(), array(), '', false);
        $this->mockXmlStreamWriter = $this->getMock('net\\stubbles\\xml\\XMLStreamWriter');
        $this->xmlSerializerFacade = new XmlSerializerFacade($this->mockXmlSerializer, $this->mockXmlStreamWriter);
    }

    /**
     * clean up test environment
     */
    public function tearDown()
    {
        libxml_clear_errors();
    }

    /**
     * @test
     */
    public function annotationsPresent()
    {
        $this->assertTrue($this->xmlSerializerFacade->getClass()
                                                    ->getConstructor()
                                                    ->hasAnnotation('Inject')
        );
    }

    /**
     * @test
     */
    public function serializeToXmlReturnsXmlString()
    {
        $this->mockXmlSerializer->expects($this->once())
                                ->method('serialize')
                                ->with($this->equalTo('foo'), $this->equalTo($this->mockXmlStreamWriter))
                                ->will($this->returnValue($this->mockXmlStreamWriter));
        $this->mockXmlStreamWriter->expects($this->once())
                                  ->method('asXML')
                                  ->will($this->returnValue('<?xml version="1.0" encoding="UTF-8"?><string>foo</string>'));
        $this->assertEquals('<?xml version="1.0" encoding="UTF-8"?><string>foo</string>',
                            $this->xmlSerializerFacade->serializeToXml('foo')
        );
    }

    /**
     * @test
     */
    public function serializeToDomReturnsDOMDocument()
    {
        $domDocument = new \DOMDocument();
        $this->mockXmlSerializer->expects($this->once())
                                ->method('serialize')
                                ->with($this->equalTo('foo'), $this->equalTo($this->mockXmlStreamWriter))
                                ->will($this->returnValue($this->mockXmlStreamWriter));
        $this->mockXmlStreamWriter->expects($this->once())
                                  ->method('asDom')
                                  ->will($this->returnValue($domDocument));
        $this->assertSame($domDocument, $this->xmlSerializerFacade->serializeToDom('foo'));
    }
}
?>