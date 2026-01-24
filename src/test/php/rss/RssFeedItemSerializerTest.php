<?php
declare(strict_types=1);
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace stubbles\xml\rss;

use bovigo\callmap\ClassProxy;
use bovigo\callmap\NewInstance;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use stdClass;
use stubbles\date\Date;
use stubbles\xml\serializer\attributes\XmlSerializer as XmlSerializerAttribute;
use stubbles\xml\XmlStreamWriter;
use stubbles\xml\serializer\XmlSerializer;

use function bovigo\assert\assertThat;
use function bovigo\assert\assertTrue;
use function bovigo\assert\expect;
use function bovigo\assert\predicate\equals;
use function bovigo\callmap\verify;
use function stubbles\reflect\attributesOf;

/**
 * Test for stubbles\xml\rss\RssFeedItemSerializer.
 */
#[Group('xml')]
#[Group('xml_rss')]
class RssFeedItemSerializerTest extends TestCase
{
    private RssFeedItemSerializer $rssFeedItemSerializer;
    private XmlSerializer&ClassProxy $xmlSerializer;
    private XmlStreamWriter&ClassProxy $xmlStreamWriter;

    protected function setUp(): void
    {
        $this->rssFeedItemSerializer = new RssFeedItemSerializer();
        $this->xmlSerializer = NewInstance::stub(XmlSerializer::class)
            ->returns(['convertBoolToString' => false]);
        $this->xmlStreamWriter = NewInstance::of(XmlStreamWriter::class);
    }

    #[Test]
    public function isDefaultSerializerForRssFeedItem(): void
    {
        assertThat(
            attributesOf(RssFeedItem::class)
                ->firstNamed(XmlSerializerAttribute::class)
                ->getClassName(),
            equals(get_class($this->rssFeedItemSerializer))
        );
    }

    #[Test]
    public function serializeThrowsIllegalArgumentExceptionIfObjectIsNotRssFeedItem(): void
    {
        expect(function() {
            $this->rssFeedItemSerializer->serialize(
                new stdClass(),
                $this->xmlSerializer,
                $this->xmlStreamWriter
            );
        })->throws(\InvalidArgumentException::class);
    }

    #[Test]
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

    #[Test]
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

    #[Test]
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

        verify($this->xmlStreamWriter, 'writeElement')->wasCalled(12);
    }
}
