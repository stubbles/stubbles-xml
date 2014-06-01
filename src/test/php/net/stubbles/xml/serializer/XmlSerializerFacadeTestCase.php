<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  stubbles\xml
 */
namespace stubbles\xml\serializer;
use stubbles\lang;
/**
 * Test for stubbles\xml\serializer\XmlSerializerFacade.
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
    private $xmlSerializerFacade;
    /**
     * mocked xml serializer
     *
     * @type  \PHPUnit_Framework_MockObject_MockObject
     */
    private $mockXmlSerializer;
    /**
     * mocked xml stream writer
     *
     * @type  \PHPUnit_Framework_MockObject_MockObject
     */
    private $mockXmlStreamWriter;

    /**
     * set up test environment
     */
    public function setUp()
    {
        libxml_clear_errors();
        $this->mockXmlSerializer   = $this->getMockBuilder('stubbles\xml\serializer\XmlSerializer')
                                          ->disableOriginalConstructor()
                                          ->getMock();
        $this->mockXmlStreamWriter = $this->getMock('stubbles\xml\XmlStreamWriter');
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
        $this->assertTrue(lang\reflectConstructor($this->xmlSerializerFacade)->hasAnnotation('Inject'));
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
