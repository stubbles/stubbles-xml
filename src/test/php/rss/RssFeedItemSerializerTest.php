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
use stubbles\date\Date;
use stubbles\xml\XmlStreamWriter;
use stubbles\xml\serializer\XmlSerializer;

use function bovigo\assert\assertThat;
use function bovigo\assert\assertTrue;
use function bovigo\assert\expect;
use function bovigo\assert\predicate\equals;
use function bovigo\callmap\verify;
use function stubbles\reflect\annotationsOf;
/**
 * Test for stubbles\xml\rss\RssFeedItemSerializer.
 *
 * @group  xml
 * @group  xml_rss
 */
class RssFeedItemSerializerTest extends TestCase
{
    /**
     * @var  RssFeedItemSerializer
     */
    private $rssFeedItemSerializer;
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
        $this->rssFeedItemSerializer = new RssFeedItemSerializer();
        $this->xmlSerializer   = NewInstance::stub(XmlSerializer::class)
                ->returns(['convertBoolToString' => false]);
        $this->xmlStreamWriter = NewInstance::of(XmlStreamWriter::class);
    }

    /**
     * @test
     */
    public function isDefaultSerializerForRssFeedItem(): void
    {
        assertThat(
                annotationsOf(RssFeedItem::class)
                        ->firstNamed('XmlSerializer')
                        ->getSerializerClass()
                        ->getName(),
                equals(get_class($this->rssFeedItemSerializer))
        );
    }

    /**
     * @test
     */
    public function serializeThrowsIllegalArgumentExceptionIfObjectIsNotRssFeedItem(): void
    {
        expect(function() {
                $this->rssFeedItemSerializer->serialize(
                        new \stdClass(),
                        $this->xmlSerializer,
                        $this->xmlStreamWriter
                );
        })->throws(\InvalidArgumentException::class);
    }

    /**
     * @test
     */
    public function serializeEmptyItem(): void
    {
        $this->rssFeedItemSerializer->serialize(
                RssFeedItem::create('title', 'link', 'description'),
                $this->xmlSerializer,
                $this->xmlStreamWriter
        );
        verify($this->xmlStreamWriter, 'writeStartElement')->received('item');
        verify($this->xmlStreamWriter, 'writeElement')->wasCalled(3);
    }

    /**
     * @test
     */
    public function usesGivenTagNameInsteadOfDefault(): void
    {
        $this->rssFeedItemSerializer->serialize(
                RssFeedItem::create('title', 'link', 'description'),
                $this->xmlSerializer,
                $this->xmlStreamWriter,
                'other'
        );
        verify($this->xmlStreamWriter, 'writeStartElement')->received('other');
        verify($this->xmlStreamWriter, 'writeElement')->wasCalled(3);
    }

    /**
     * @test
     */
    public function serializeCompleteItem(): void
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
                $this->xmlStreamWriter
        );
        assertTrue(verify($this->xmlStreamWriter, 'writeElement')->wasCalled(12));
    }
}
