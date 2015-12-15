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
use bovigo\callmap\NewInstance;
use stubbles\xml\XmlStreamWriter;
use stubbles\xml\serializer\XmlSerializer;

use function bovigo\callmap\verify;
use function stubbles\lang\reflect\annotationsOf;
/**
 * Test for stubbles\xml\rss\RssFeedSerializer.
 *
 * @group  xml
 * @group  xml_rss
 */
class RssFeedSerializerTest extends \PHPUnit_Framework_TestCase
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
        $this->rssFeedSerializer = new RssFeedSerializer();
        $this->xmlSerializer     = NewInstance::stub(XmlSerializer::class);
        $this->xmlStreamWriter   = NewInstance::of(XmlStreamWriter::class);
    }

    /**
     * @test
     */
    public function isDefaultSerializerForRssFeedItem()
    {
        assertEquals(
                get_class($this->rssFeedSerializer),
                annotationsOf(RssFeed::class)
                        ->firstNamed('XmlSerializer')
                        ->getSerializerClass()
                        ->getName()
        );
    }

    /**
     * @test
     * @expectedException  InvalidArgumentException
     */
    public function serializeThrowsIllegalArgumentExceptionIfObjectIsNotRssFeed()
    {
        $this->rssFeedSerializer->serialize(
                new \stdClass(),
                $this->xmlSerializer,
                $this->xmlStreamWriter,
                null
        );
    }

    /**
     * @test
     */
    public function noItemsNoStylesheets()
    {
        assertSame(
                $this->xmlStreamWriter,
                $this->rssFeedSerializer
                        ->setGenerator('Another generator')
                        ->serialize(
                                new RssFeed('title', 'link', 'description'),
                                $this->xmlSerializer,
                                $this->xmlStreamWriter,
                                null
                        )
        );
        verify($this->xmlSerializer, 'serializeObject')->wasNeverCalled();
        verify($this->xmlStreamWriter, 'writeProcessingInstruction')->wasNeverCalled();
        verify($this->xmlStreamWriter, 'writeStartElement')->wasCalled(2);
        verify($this->xmlStreamWriter, 'writeEndElement')->wasCalled(2);
        verify($this->xmlStreamWriter, 'writeElement')->wasCalled(4);
    }

    /**
     * @test
     */
    public function noItemsWithStylesheets()
    {
        $rssFeed = new RssFeed('title', 'link', 'description');
        $rssFeed->appendStylesheet('foo.xsl');
        assertSame(
                $this->xmlStreamWriter,
                $this->rssFeedSerializer->serialize(
                        $rssFeed,
                        $this->xmlSerializer,
                        $this->xmlStreamWriter,
                        null
                )
        );
        verify($this->xmlSerializer, 'serializeObject')->wasNeverCalled();
        verify($this->xmlStreamWriter, 'writeProcessingInstruction')->wasCalledOnce();
        verify($this->xmlStreamWriter, 'writeStartElement')->wasCalled(2);
        verify($this->xmlStreamWriter, 'writeEndElement')->wasCalled(2);
        verify($this->xmlStreamWriter, 'writeElement')->wasCalled(4);
    }

    /**
     * @test
     */
    public function withItemsNoStylesheets()
    {
        $rssFeed = new RssFeed('title', 'link', 'description');
        $rssFeed->addItem('foo', 'bar', 'baz');
        assertSame(
                $this->xmlStreamWriter,
                $this->rssFeedSerializer->serialize(
                        $rssFeed,
                        $this->xmlSerializer,
                        $this->xmlStreamWriter,
                        null
                )
        );
        verify($this->xmlSerializer, 'serializeObject')->wasCalledOnce();
        verify($this->xmlStreamWriter, 'writeProcessingInstruction')->wasNeverCalled();
        verify($this->xmlStreamWriter, 'writeStartElement')->wasCalled(2);
        verify($this->xmlStreamWriter, 'writeEndElement')->wasCalled(2);
        verify($this->xmlStreamWriter, 'writeElement')->wasCalled(4);
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
        assertSame(
                $this->xmlStreamWriter,
                $this->rssFeedSerializer->serialize(
                        $rssFeed,
                        $this->xmlSerializer,
                        $this->xmlStreamWriter,
                        null
                )
        );
        verify($this->xmlStreamWriter, 'writeProcessingInstruction')->wasNeverCalled();
        verify($this->xmlStreamWriter, 'writeStartElement')->wasCalled(3);
        verify($this->xmlStreamWriter, 'writeEndElement')->wasCalled(3);
        verify($this->xmlStreamWriter, 'writeElement')->wasCalled(16);
    }
}
