<?php
declare(strict_types=1);
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace stubbles\xml\rss;

use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use stubbles\date\Date;

use function bovigo\assert\assertThat;
use function bovigo\assert\assertEmptyArray;
use function bovigo\assert\assertEmptyString;
use function bovigo\assert\assertFalse;
use function bovigo\assert\assertNull;
use function bovigo\assert\assertTrue;
use function bovigo\assert\expect;
use function bovigo\assert\predicate\equals;
/**
 * Test for stubbles\xml\rss\RssFeedItem.
 */
#[Group('xml')]
#[Group('xml_rss')]
class RssFeedItemTest extends TestCase
{
    private RssFeedItem $rssFeedItem;

    protected function setUp(): void
    {
        $this->rssFeedItem = RssFeedItem::create(
            'test',
            'http://stubbles.net/',
            'description'
        );
    }

    #[Test]
    public function hasGivenTitleByDefault(): void
    {
        assertThat($this->rssFeedItem->title(), equals('test'));
    }

    #[Test]
    public function hasGivenLinkByDefault(): void
    {
        assertThat($this->rssFeedItem->link(), equals('http://stubbles.net/'));
    }

    #[Test]
    public function hasGivenDescriptionByDefault(): void
    {
        assertThat($this->rssFeedItem->description(), equals('description'));
    }

    #[Test]
    public function hasNoAuthorByDefault(): void
    {
        assertFalse($this->rssFeedItem->hasAuthor());
    }

    #[Test]
    public function initialAuthorIsNull(): void
    {
        assertNull($this->rssFeedItem->author());
    }

    #[Test]
    public function setAuthorWithoutMailAddressUsesExampleMailAddress(): void
    {
        assertThat(
            $this->rssFeedItem->byAuthor('mikey')->author(),
            equals('nospam@example.com (mikey)')
        );
    }

    #[Test]
    public function canSetAuthorWithMailAddress(): void
    {
        assertThat(
            $this->rssFeedItem->byAuthor('test@example.net (mikey)')
                ->author(),
            equals('test@example.net (mikey)')
        );
    }

    #[Test]
    public function hasNoCategoriesByDefault(): void
    {
        assertEmptyArray($this->rssFeedItem->categories());
    }

    #[Test]
    public function canSetCategories(): void
    {
        assertThat(
            $this->rssFeedItem->inCategory('cat1')
                ->inCategory('cat2', 'domain')
                ->categories(),
            equals([
                ['category' => 'cat1', 'domain'   => ''],
                ['category' => 'cat2', 'domain'   => 'domain']
            ])
        );
    }

    #[Test]
    public function canSetListOfCategories(): void
    {
        assertThat(
            $this->rssFeedItem->inCategories(['cat1', 'cat2'])
                ->categories(),
            equals([
                ['category' => 'cat1', 'domain'   => ''],
                ['category' => 'cat2', 'domain'   => '']
            ])
        );
    }

    #[Test]
    public function hasNoCommentsUrlByDefault(): void
    {
        assertFalse($this->rssFeedItem->hasComments());
    }

    #[Test]
    public function initialCommentUrlIsNull(): void
    {
        assertNull($this->rssFeedItem->comments());
    }

    #[Test]
    public function canSetCommentsUrl(): void
    {
        assertThat(
            $this->rssFeedItem->addCommentsAt('http://stubbles.net/comments/')
                ->comments(),
            equals('http://stubbles.net/comments/')
        );
    }

    #[Test]
    public function hasNoEnclosuresByDefault(): void
    {
        assertEmptyArray($this->rssFeedItem->enclosures());
    }

    #[Test]
    public function canSetEnclosures(): void
    {
        assertThat(
            $this->rssFeedItem->deliveringEnclosure(
                'http://stubbles.net/enclosure.mp3',
                50,
                'audio/mpeg'
            )->enclosures(),
            equals([[
                'url'    => 'http://stubbles.net/enclosure.mp3',
                'length' => 50,
                'type' => 'audio/mpeg'
            ]])
        );
    }

    #[Test]
    public function hasNoGuidByDefault(): void
    {
        assertFalse($this->rssFeedItem->hasGuid());
    }

    #[Test]
    public function initialGuidIsNull(): void
    {
        assertNull($this->rssFeedItem->guid());
    }

    #[Test]
    public function guidIsNotPermalinkByDefault(): void
    {
        assertFalse($this->rssFeedItem->isGuidPermaLink());
    }

    #[Test]
    public function canSetGuid(): void
    {
        assertThat($this->rssFeedItem->withGuid('dummy')->guid(), equals('dummy'));
    }

    #[Test]
    public function settingGuidEnablesGuidAsPermalink(): void
    {
        assertTrue($this->rssFeedItem->withGuid('dummy')->isGuidPermaLink());
    }

    #[Test]
    public function settingGuidAndDisablingPermalink(): void
    {
        assertFalse(
                $this->rssFeedItem->withGuid('dummy')
                        ->andGuidIsNotPermaLink()
                        ->isGuidPermaLink()
        );
    }

    #[Test]
    public function hasNoPublishingDateByDefault(): void
    {
        assertFalse($this->rssFeedItem->hasPubDate());
    }

    #[Test]
    public function initialPublishingDateIsNull(): void
    {
        assertNull($this->rssFeedItem->pubDate());
    }

    #[Test]
    public function publishingDateCanBePassedAsDateInstance(): void
    {
        $date = new Date('2008-05-24');
        assertThat(
            $this->rssFeedItem->publishedOn($date)->pubDate(),
            equals('Sat 24 May 2008 00:00:00 ' . $date->offset())
        );
    }

    #[Test]
    public function alternativePublishingDate(): void
    {
        $date = new Date('2008-05-24');
        assertThat(
            $this->rssFeedItem->publishedOn('2008-05-24')->pubDate(),
            equals('Sat 24 May 2008 00:00:00 ' . $date->offset())
        );
    }

    #[Test]
    public function settingInvalidPublishingDateThrowsIllegalArgumentException(): void
    {
        expect(function() { $this->rssFeedItem->publishedOn('foo'); })
            ->throws(\InvalidArgumentException::class);
    }

    #[Test]
    public function hasNoSourcesByDefault(): void
    {
        assertEmptyArray($this->rssFeedItem->sources());
    }

    #[Test]
    public function canSetSources(): void
    {
        assertThat(
            $this->rssFeedItem->inspiredBySource(
                'stubbles',
                'http://stubbles.net/source/'
            )->sources(),
            equals([['name' => 'stubbles', 'url'  => 'http://stubbles.net/source/']])
        );
    }

    #[Test]
    public function hasNoContentByDefault(): void
    {
        assertFalse($this->rssFeedItem->hasContent());
    }

    #[Test]
    public function initialContentIsEmpty(): void
    {
        assertEmptyString($this->rssFeedItem->content());
    }

    #[Test]
    public function canSetContent(): void
    {
        assertThat(
            $this->rssFeedItem->withContent('<foo>bar</foo><baz/>')
                ->content(),
            equals('<foo>bar</foo><baz/>')
        );
    }
}
