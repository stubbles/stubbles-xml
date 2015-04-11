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
use stubbles\lang\reflect;
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
        $this->xmlSerializer     = NewInstance::stub('stubbles\xml\serializer\XmlSerializer');
        $this->xmlStreamWriter   = NewInstance::of('stubbles\xml\XmlStreamWriter');
    }

    /**
     * @test
     */
    public function isDefaultSerializerForRssFeedItem()
    {
        assertEquals(
                get_class($this->rssFeedSerializer),
                reflect\annotationsOf('stubbles\xml\rss\RssFeed')
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
        assertEquals(0, $this->xmlSerializer->callsReceivedFor('serializeObject'));
        assertEquals(0, $this->xmlStreamWriter->callsReceivedFor('writeProcessingInstruction'));
        assertEquals(2, $this->xmlStreamWriter->callsReceivedFor('writeStartElement'));
        assertEquals(2, $this->xmlStreamWriter->callsReceivedFor('writeEndElement'));
        assertEquals(4, $this->xmlStreamWriter->callsReceivedFor('writeElement'));
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
        assertEquals(0, $this->xmlSerializer->callsReceivedFor('serializeObject'));
        assertEquals(1, $this->xmlStreamWriter->callsReceivedFor('writeProcessingInstruction'));
        assertEquals(2, $this->xmlStreamWriter->callsReceivedFor('writeStartElement'));
        assertEquals(2, $this->xmlStreamWriter->callsReceivedFor('writeEndElement'));
        assertEquals(4, $this->xmlStreamWriter->callsReceivedFor('writeElement'));
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
        assertEquals(1, $this->xmlSerializer->callsReceivedFor('serializeObject'));
        assertEquals(0, $this->xmlStreamWriter->callsReceivedFor('writeProcessingInstruction'));
        assertEquals(2, $this->xmlStreamWriter->callsReceivedFor('writeStartElement'));
        assertEquals(2, $this->xmlStreamWriter->callsReceivedFor('writeEndElement'));
        assertEquals(4, $this->xmlStreamWriter->callsReceivedFor('writeElement'));
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
        assertEquals(0, $this->xmlStreamWriter->callsReceivedFor('writeProcessingInstruction'));
        assertEquals(3, $this->xmlStreamWriter->callsReceivedFor('writeStartElement'));
        assertEquals(3, $this->xmlStreamWriter->callsReceivedFor('writeEndElement'));
        assertEquals(16, $this->xmlStreamWriter->callsReceivedFor('writeElement'));
    }
}
