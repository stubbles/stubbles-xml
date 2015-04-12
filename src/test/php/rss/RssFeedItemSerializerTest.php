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
use bovigo\callmap;
use bovigo\callmap\NewInstance;
use stubbles\date\Date;
use stubbles\lang\reflect;
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
     * @type  \bovigo\callmap\Proxy
     */
    private $xmlSerializer;
    /**
     * mocked xml serializer
     *
     * @type  \bovigo\callmap\Proxy
     */
    private $xmlStreamWriter;

    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->rssFeedItemSerializer = new RssFeedItemSerializer();
        $this->xmlSerializer   = NewInstance::stub('stubbles\xml\serializer\XmlSerializer');
        $this->xmlStreamWriter = NewInstance::of('stubbles\xml\XmlStreamWriter');
    }

    /**
     * @test
     */
    public function isDefaultSerializerForRssFeedItem()
    {
        assertEquals(
                get_class($this->rssFeedItemSerializer),
                reflect\annotationsOf('stubbles\xml\rss\RssFeedItem')
                        ->firstNamed('XmlSerializer')
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
        $this->rssFeedItemSerializer->serialize(
                new \stdClass(),
                $this->xmlSerializer,
                $this->xmlStreamWriter,
                null
        );
    }

    /**
     * @test
     */
    public function serializeEmptyItem()
    {
        $this->rssFeedItemSerializer->serialize(
                RssFeedItem::create('title', 'link', 'description'),
                $this->xmlSerializer,
                $this->xmlStreamWriter,
                null
        );
        callmap\verify($this->xmlStreamWriter, 'writeStartElement')
                ->received('item');
        callmap\verify($this->xmlStreamWriter, 'writeElement')
                ->wasCalled(3);
    }

    /**
     * @test
     */
    public function usesGivenTagNameInsteadOfDefault()
    {
        $this->rssFeedItemSerializer->serialize(
                RssFeedItem::create('title', 'link', 'description'),
                $this->xmlSerializer,
                $this->xmlStreamWriter,
                'other'
        );
        callmap\verify($this->xmlStreamWriter, 'writeStartElement')
                ->received('other');
        callmap\verify($this->xmlStreamWriter, 'writeElement')
                ->wasCalled(3);
    }

    /**
     * @test
     */
    public function serializeCompleteItem()
    {
        $this->rssFeedItemSerializer->serialize(
                RssFeedItem::create('title', 'link', 'description')
                        ->byAuthor('mikey')
                        ->inCategory('cat1')
                        ->inCategory('cat2', 'domain')
                        ->addCommentsAt('http://stubbles.net/comments/')
                        ->deliveringEnclosure(
                                'http://stubbles.net/enclosure.mp3',
                                50,
                                'audio/mpeg'
                        )
                        ->withGuid('dummy')
                        ->publishedOn(new Date('2008-05-24'))
                        ->inspiredBySource(
                                'stubbles',
                                'http://stubbles.net/source/'
                        )
                        ->withContent('<foo>bar</foo><baz/>'),
                $this->xmlSerializer,
                $this->xmlStreamWriter,
                null
        );
        callmap\verify($this->xmlStreamWriter, 'writeElement')->wasCalled(12);
    }
}
