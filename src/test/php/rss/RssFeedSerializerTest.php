<?php
declare(strict_types=1);
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace stubbles\xml\rss;
use bovigo\callmap\NewInstance;
use PHPUnit\Framework\TestCase;
use stubbles\xml\XmlStreamWriter;
use stubbles\xml\serializer\XmlSerializer;

use function bovigo\assert\assertThat;
use function bovigo\assert\expect;
use function bovigo\assert\predicate\equals;
use function bovigo\assert\predicate\isSameAs;
use function bovigo\callmap\verify;
use function stubbles\reflect\annotationsOf;
/**
 * Test for stubbles\xml\rss\RssFeedSerializer.
 *
 * @group  xml
 * @group  xml_rss
 */
class RssFeedSerializerTest extends TestCase
{
    /**
     * @var  RssFeedSerializer
     */
    private $rssFeedSerializer;
    /**
     * @var  XmlSerializer&\bovigo\callmap\ClassProxy
     */
    private $xmlSerializer;
    /**
     * @var  XmlStreamWriter&\bovigo\callmap\ClassProxy
     */
    private $xmlStreamWriter;

    protected function setUp(): void
    {
        $this->rssFeedSerializer = new RssFeedSerializer();
        $this->xmlStreamWriter   = NewInstance::of(XmlStreamWriter::class);
        $this->xmlSerializer     = NewInstance::stub(XmlSerializer::class)
                ->returns(['serializeObject' => $this->xmlStreamWriter]);

    }

    /**
     * @test
     */
    public function isDefaultSerializerForRssFeedItem(): void
    {
        assertThat(
                annotationsOf(RssFeed::class)
                        ->firstNamed('XmlSerializer')
                        ->getSerializerClass()
                        ->getName(),
                equals(get_class($this->rssFeedSerializer))
        );
    }

    /**
     * @test
     */
    public function serializeThrowsIllegalArgumentExceptionIfObjectIsNotRssFeed(): void
    {
        expect(function() {
                $this->rssFeedSerializer->serialize(
                        new \stdClass(),
                        $this->xmlSerializer,
                        $this->xmlStreamWriter
                );
        })->throws(\InvalidArgumentException::class);
    }

    /**
     * @test
     */
    public function noItemsNoStylesheets(): void
    {
        $this->rssFeedSerializer
            ->setGenerator('Another generator')
            ->serialize(
                new RssFeed('title', 'link', 'description'),
                $this->xmlSerializer,
                $this->xmlStreamWriter
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
    public function noItemsWithStylesheets(): void
    {
        $rssFeed = new RssFeed('title', 'link', 'description');
        $rssFeed->appendStylesheet('foo.xsl');
        $this->rssFeedSerializer->serialize(
            $rssFeed,
            $this->xmlSerializer,
            $this->xmlStreamWriter
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
    public function withItemsNoStylesheets(): void
    {
        $rssFeed = new RssFeed('title', 'link', 'description');
        $rssFeed->addItem('foo', 'bar', 'baz');
        $this->rssFeedSerializer->serialize(
            $rssFeed,
            $this->xmlSerializer,
            $this->xmlStreamWriter
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
    public function withAllChannelElements(): void
    {
        $rssFeed = new RssFeed('title', 'link', 'description');
        $rssFeed->setLocale('en_EN')
                ->setCopyright('(c) 2007 Stubbles Development Team')
                ->setManagingEditor('mikey')
                ->setWebMaster('schst')
                ->setLastBuildDate(50)
                ->setTimeToLive(60)
                ->setImage('http://example.org/example.gif', 'foo');
        $this->rssFeedSerializer->serialize(
            $rssFeed,
            $this->xmlSerializer,
            $this->xmlStreamWriter
        );
        verify($this->xmlStreamWriter, 'writeProcessingInstruction')->wasNeverCalled();
        verify($this->xmlStreamWriter, 'writeStartElement')->wasCalled(3);
        verify($this->xmlStreamWriter, 'writeEndElement')->wasCalled(3);
        verify($this->xmlStreamWriter, 'writeElement')->wasCalled(16);
    }
}
