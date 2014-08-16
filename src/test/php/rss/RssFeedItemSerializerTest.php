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
use stubbles\date\Date;
use stubbles\lang;
/**
 * Test for stubbles\xml\rss\RssFeedItemSerializer.
 *
 * @group  xml
 * @group  xml_rss
 */
class RssFeedItemSerializerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * instance to test
     *
     * @type  RssFeedItemSerializer
     */
    private $rssFeedItemSerializer;
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
        $this->rssFeedItemSerializer = new RssFeedItemSerializer();
        $this->mockXmlSerializer     = $this->getMockBuilder('stubbles\xml\serializer\XmlSerializer')
                                            ->disableOriginalConstructor()
                                            ->getMock();
        $this->mockXmlStreamWriter   = $this->getMock('stubbles\xml\XmlStreamWriter');
    }

    /**
     * @test
     */
    public function isDefaultSerializerForRssFeedItem()
    {
        $class = lang\reflect('stubbles\xml\rss\RssFeedItem');
        $this->assertTrue($class->hasAnnotation('XmlSerializer'));
        $this->assertEquals(get_class($this->rssFeedItemSerializer),
                            $class->getAnnotation('XmlSerializer')
                                  ->getSerializerClass()
                                  ->getName()
        );
    }

    /**
     * @test
     * @expectedException  InvalidArgumentException
     */
    public function serializeThrowsIllegalArgumentExceptionIfObjectIsNotRssFeedItem()
    {
        $this->rssFeedItemSerializer->serialize(new \stdClass(),
                                                $this->mockXmlSerializer,
                                                $this->mockXmlStreamWriter,
                                                null
        );
    }

    /**
     * @test
     */
    public function serializeEmptyItem()
    {
        $this->mockXmlStreamWriter->expects($this->once())
                                  ->method('writeStartElement')
                                  ->with($this->equalTo('item'));
        $this->mockXmlStreamWriter->expects($this->exactly(3))
                                  ->method('writeElement');
        $this->rssFeedItemSerializer->serialize(RssFeedItem::create('title', 'link', 'description'),
                                                $this->mockXmlSerializer,
                                                $this->mockXmlStreamWriter,
                                                null
        );
    }

    /**
     * @test
     */
    public function usesGivenTagNameInsteadOfDefault()
    {
        $this->mockXmlStreamWriter->expects($this->once())
                                  ->method('writeStartElement')
                                  ->with($this->equalTo('other'));
        $this->mockXmlStreamWriter->expects($this->exactly(3))
                                  ->method('writeElement');
        $this->rssFeedItemSerializer->serialize(RssFeedItem::create('title', 'link', 'description'),
                                                $this->mockXmlSerializer,
                                                $this->mockXmlStreamWriter,
                                                'other'
        );
    }

    /**
     * @test
     */
    public function serializeCompleteItem()
    {
        $this->mockXmlStreamWriter->expects($this->once())
                                  ->method('writeStartElement');
        $this->mockXmlStreamWriter->expects($this->exactly(12))
                                  ->method('writeElement');
        $this->rssFeedItemSerializer->serialize(RssFeedItem::create('title', 'link', 'description')
                                                           ->byAuthor('mikey')
                                                           ->inCategory('cat1')
                                                           ->inCategory('cat2', 'domain')
                                                           ->addCommentsAt('http://stubbles.net/comments/')
                                                           ->deliveringEnclosure('http://stubbles.net/enclosure.mp3',
                                                                                 50,
                                                                                 'audio/mpeg'
                                                             )
                                                           ->withGuid('dummy')
                                                           ->publishedOn(new Date('2008-05-24'))
                                                           ->inspiredBySource('stubbles',
                                                                              'http://stubbles.net/source/'
                                                             )
                                                           ->withContent('<foo>bar</foo><baz/>'),
                                                $this->mockXmlSerializer,
                                                $this->mockXmlStreamWriter,
                                                null
        );
    }
}
