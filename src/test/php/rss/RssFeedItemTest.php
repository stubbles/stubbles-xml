<?php
declare(strict_types=1);
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace stubbles\xml\rss;
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
 *
 * @group  xml
 * @group  xml_rss
 */
class RssFeedItemTest extends TestCase
{
    /**
     * instance to test
     *
     * @type  RssFeedItem
     */
    private $rssFeedItem;

    protected function setUp(): void
    {
        $this->rssFeedItem = RssFeedItem::create(
                'test',
                'http://stubbles.net/',
                'description'
        );
    }

    /**
     * @test
     */
    public function hasGivenTitleByDefault()
    {
        assertThat($this->rssFeedItem->title(), equals('test'));
    }

    /**
     * @test
     */
    public function hasGivenLinkByDefault()
    {
        assertThat($this->rssFeedItem->link(), equals('http://stubbles.net/'));
    }

    /**
     * @test
     */
    public function hasGivenDescriptionByDefault()
    {
        assertThat($this->rssFeedItem->description(), equals('description'));
    }

    /**
     * @test
     */
    public function hasNoAuthorByDefault()
    {
        assertFalse($this->rssFeedItem->hasAuthor());
    }

    /**
     * @test
     */
    public function initialAuthorIsNull()
    {
        assertNull($this->rssFeedItem->author());
    }

    /**
     * @test
     */
    public function setAuthorWithoutMailAddressUsesExampleMailAddress()
    {
        assertThat(
                $this->rssFeedItem->byAuthor('mikey')->author(),
                equals('nospam@example.com (mikey)')
        );
    }

    /**
     * @test
     */
    public function canSetAuthorWithMailAddress()
    {
        assertThat(
                $this->rssFeedItem->byAuthor('test@example.net (mikey)')
                        ->author(),
                equals('test@example.net (mikey)')
        );
    }

    /**
     * @test
     */
    public function hasNoCategoriesByDefault()
    {
        assertEmptyArray($this->rssFeedItem->categories());
    }

    /**
     * @test
     */
    public function canSetCategories()
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

    /**
     * @test
     */
    public function canSetListOfCategories()
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

    /**
     * @test
     */
    public function hasNoCommentsUrlByDefault()
    {
        assertFalse($this->rssFeedItem->hasComments());
    }

    /**
     * @test
     */
    public function initialCommentUrlIsNull()
    {
        assertNull($this->rssFeedItem->comments());
    }

    /**
     * @test
     */
    public function canSetCommentsUrl()
    {
        assertThat(
                $this->rssFeedItem->addCommentsAt('http://stubbles.net/comments/')
                        ->comments(),
                equals('http://stubbles.net/comments/')
        );
    }

    /**
     * @test
     */
    public function hasNoEnclosuresByDefault()
    {
        assertEmptyArray($this->rssFeedItem->enclosures());
    }

    /**
     * @test
     */
    public function canSetEnclosures()
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

    /**
     * @test
     */
    public function hasNoGuidByDefault()
    {
        assertFalse($this->rssFeedItem->hasGuid());
    }

    /**
     * @test
     */
    public function initialGuidIsNull()
    {
        assertNull($this->rssFeedItem->guid());
    }

    /**
     * @test
     */
    public function guidIsNotPermalinkByDefault()
    {
        assertFalse($this->rssFeedItem->isGuidPermaLink());
    }

    /**
     * @test
     */
    public function canSetGuid()
    {
        assertThat($this->rssFeedItem->withGuid('dummy')->guid(), equals('dummy'));
    }

    /**
     * @test
     */
    public function settingGuidEnablesGuidAsPermalink()
    {
        assertTrue($this->rssFeedItem->withGuid('dummy')->isGuidPermaLink());
    }

    /**
     * @test
     */
    public function settingGuidAndDisablingPermalink()
    {
        assertFalse(
                $this->rssFeedItem->withGuid('dummy')
                        ->andGuidIsNotPermaLink()
                        ->isGuidPermaLink()
        );
    }

    /**
     * @test
     */
    public function hasNoPublishingDateByDefault()
    {
        assertFalse($this->rssFeedItem->hasPubDate());
    }

    /**
     * @test
     */
    public function initialPublishingDateIsNull()
    {
        assertNull($this->rssFeedItem->pubDate());
    }

    /**
     * @test
     */
    public function publishingDateCanBePassedAsDateInstance()
    {
        $date = new Date('2008-05-24');
        assertThat(
                $this->rssFeedItem->publishedOn($date)->pubDate(),
                equals('Sat 24 May 2008 00:00:00 ' . $date->offset())
        );
    }

    /**
     * @test
     */
    public function alternativePublishingDate()
    {
        $date = new Date('2008-05-24');
        assertThat(
                $this->rssFeedItem->publishedOn('2008-05-24')->pubDate(),
                equals('Sat 24 May 2008 00:00:00 ' . $date->offset())
        );
    }

    /**
     * @test
     */
    public function settingInvalidPublishingDateThrowsIllegalArgumentException()
    {
        expect(function() { $this->rssFeedItem->publishedOn('foo'); })
                ->throws(\InvalidArgumentException::class);
    }

    /**
     * @test
     */
    public function hasNoSourcesByDefault()
    {
        assertEmptyArray($this->rssFeedItem->sources());
    }

    /**
     * @test
     */
    public function canSetSources()
    {
        assertThat(
                $this->rssFeedItem->inspiredBySource(
                        'stubbles',
                        'http://stubbles.net/source/'
                )->sources(),
                equals([['name' => 'stubbles', 'url'  => 'http://stubbles.net/source/']])
        );
    }

    /**
     * @test
     */
    public function hasNoContentByDefault()
    {
        assertFalse($this->rssFeedItem->hasContent());
    }

    /**
     * @test
     */
    public function initialContentIsEmpty()
    {
        assertEmptyString($this->rssFeedItem->content());
    }

    /**
     * @test
     */
    public function canSetContent()
    {
        assertThat(
                $this->rssFeedItem->withContent('<foo>bar</foo><baz/>')
                        ->content(),
                equals('<foo>bar</foo><baz/>')
        );
    }
}
