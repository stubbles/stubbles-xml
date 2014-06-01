<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  stubbles\xml
 */
namespace stubbles\xml\rss;
use stubbles\lang;
/**
 * Test for stubbles\xml\rss\RssFeedSerializer.
 *
 * @group  xml
 * @group  xml_rss
 */
class RssFeedSerializerTestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * instance to test
     *
     * @type  RssFeedSerializer
     */
    private $rssFeedSerializer;
    /**
     * mocked xml serializer
     *
     * @type  \PHPUnit_Framework_MockObject_MockObject
     */
    private $mockXmlSerializer;
    /**
     * mocked xml serializer
     *
     * @type  \PHPUnit_Framework_MockObject_MockObject
     */
    private $mockXmlStreamWriter;

    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->rssFeedSerializer   = new RssFeedSerializer();
        $this->mockXmlSerializer   = $this->getMockBuilder('stubbles\xml\serializer\XmlSerializer')
                                            ->disableOriginalConstructor()
                                            ->getMock();
        $this->mockXmlStreamWriter = $this->getMock('stubbles\xml\XmlStreamWriter');
    }

    /**
     * @test
     */
    public function isDefaultSerializerForRssFeedItem()
    {
        $class = lang\reflect('stubbles\xml\rss\RssFeed');
        $this->assertTrue($class->hasAnnotation('XmlSerializer'));
        $this->assertEquals(get_class($this->rssFeedSerializer),
                            $class->getAnnotation('XmlSerializer')
                                  ->getSerializerClass()
                                  ->getName()
        );
    }

    /**
     * @test
     * @expectedException  stubbles\lang\exception\IllegalArgumentException
     */
    public function serializeThrowsIllegalArgumentExceptionIfObjectIsNotRssFeed()
    {
        $this->rssFeedSerializer->serialize(new \stdClass(),
                                            $this->mockXmlSerializer,
                                            $this->mockXmlStreamWriter,
                                            null
        );
    }

    /**
     * @test
     */
    public function noItemsNoStylesheets()
    {
        $this->mockXmlStreamWriter->expects($this->never())->method('writeProcessingInstruction');
        $this->mockXmlStreamWriter->expects($this->exactly(2))->method('writeStartElement');
        $this->mockXmlStreamWriter->expects($this->exactly(2))->method('writeEndElement');
        $this->mockXmlStreamWriter->expects($this->exactly(4))->method('writeElement');
        $this->mockXmlSerializer->expects($this->never())->method('serializeObject');
        $this->assertSame($this->mockXmlStreamWriter,
                          $this->rssFeedSerializer->setGenerator('Another generator')
                                                  ->serialize(new RssFeed('title', 'link', 'description'),
                                                              $this->mockXmlSerializer,
                                                              $this->mockXmlStreamWriter,
                                                              null
                                                    )
        );
    }

    /**
     * @test
     */
    public function noItemsWithStylesheets()
    {
        $rssFeed = new RssFeed('title', 'link', 'description');
        $rssFeed->appendStylesheet('foo.xsl');
        $this->mockXmlStreamWriter = $this->getMock('stubbles\xml\XmlStreamWriter');
        $this->mockXmlStreamWriter->expects($this->once())->method('writeProcessingInstruction');
        $this->mockXmlStreamWriter->expects($this->exactly(2))->method('writeStartElement');
        $this->mockXmlStreamWriter->expects($this->exactly(2))->method('writeEndElement');
        $this->mockXmlStreamWriter->expects($this->exactly(4))->method('writeElement');
        $this->mockXmlSerializer->expects($this->never())->method('serializeObject');
        $this->assertSame($this->mockXmlStreamWriter,
                          $this->rssFeedSerializer->serialize($rssFeed,
                                                              $this->mockXmlSerializer,
                                                              $this->mockXmlStreamWriter,
                                                              null
                                                    )
        );
    }

    /**
     * @test
     */
    public function withItemsNoStylesheets()
    {
        $rssFeed = new RssFeed('title', 'link', 'description');
        $rssFeed->addItem('foo', 'bar', 'baz');
        $this->mockXmlStreamWriter = $this->getMock('stubbles\xml\XmlStreamWriter');
        $this->mockXmlStreamWriter->expects($this->never())->method('writeProcessingInstruction');
        $this->mockXmlStreamWriter->expects($this->exactly(2))->method('writeStartElement');
        $this->mockXmlStreamWriter->expects($this->exactly(2))->method('writeEndElement');
        $this->mockXmlStreamWriter->expects($this->exactly(4))->method('writeElement');
        $this->mockXmlSerializer->expects($this->once())->method('serializeObject');
        $this->assertSame($this->mockXmlStreamWriter,
                          $this->rssFeedSerializer->serialize($rssFeed,
                                                              $this->mockXmlSerializer,
                                                              $this->mockXmlStreamWriter,
                                                              null
                                                    )
        );
    }

    /**
     * @test
     */
    public function withAllChannelElements()
    {
        $rssFeed = new RssFeed('title', 'link', 'description');
        $rssFeed->setLocale('en_EN')
                ->setCopyright('(c) 2007 Stubbles Development Team')
                ->setManagingEditor('mikey')
                ->setWebMaster('schst')
                ->setLastBuildDate(50)
                ->setTimeToLive(60)
                ->setImage('http://example.org/example.gif', 'foo');

        $this->mockXmlStreamWriter = $this->getMock('stubbles\xml\XmlStreamWriter');
        $this->mockXmlStreamWriter->expects($this->never())->method('writeProcessingInstruction');
        $this->mockXmlStreamWriter->expects($this->exactly(3))->method('writeStartElement');
        $this->mockXmlStreamWriter->expects($this->exactly(3))->method('writeEndElement');
        $this->mockXmlStreamWriter->expects($this->exactly(16))->method('writeElement');
        $this->assertSame($this->mockXmlStreamWriter,
                          $this->rssFeedSerializer->serialize($rssFeed,
                                                              $this->mockXmlSerializer,
                                                              $this->mockXmlStreamWriter,
                                                              null
                                                    )
        );
    }
}
