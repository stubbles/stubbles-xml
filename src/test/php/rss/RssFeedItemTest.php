<?php
declare(strict_types=1);
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

use function bovigo\assert\assert;
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
class RssFeedItemTest extends \PHPUnit_Framework_TestCase
{
    /**
     * instance to test
     *
     * @type  RssFeedItem
     */
    private $rssFeedItem;

    /**
     * set up test environment
     */
    public function setUp()
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
        assert($this->rssFeedItem->getTitle(), equals('test'));
    }

    /**
     * @test
     */
    public function hasGivenLinkByDefault()
    {
        assert($this->rssFeedItem->getLink(), equals('http://stubbles.net/'));
    }

    /**
     * @test
     */
    public function hasGivenDescriptionByDefault()
    {
        assert($this->rssFeedItem->getDescription(), equals('description'));
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
        assertNull($this->rssFeedItem->getAuthor());
    }

    /**
     * @test
     */
    public function setAuthorWithoutMailAddressUsesExampleMailAddress()
    {
        assert(
                $this->rssFeedItem->byAuthor('mikey')->getAuthor(),
                equals('nospam@example.com (mikey)')
        );
    }

    /**
     * @test
     */
    public function canSetAuthorWithMailAddress()
    {
        assert(
                $this->rssFeedItem->byAuthor('test@example.net (mikey)')
                        ->getAuthor(),
                equals('test@example.net (mikey)')
        );
    }

    /**
     * @test
     */
    public function hasNoCategoriesByDefault()
    {
        assertEmptyArray($this->rssFeedItem->getCategories());
    }

    /**
     * @test
     */
    public function canSetCategories()
    {
        assert(
                $this->rssFeedItem->inCategory('cat1')
                        ->inCategory('cat2', 'domain')
                        ->getCategories(),
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
        assert(
                $this->rssFeedItem->inCategories(['cat1', 'cat2'])
                        ->getCategories(),
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
        assertNull($this->rssFeedItem->getComments());
    }

    /**
     * @test
     */
    public function canSetCommentsUrl()
    {
        assert(
                $this->rssFeedItem->addCommentsAt('http://stubbles.net/comments/')
                        ->getComments(),
                equals('http://stubbles.net/comments/')
        );
    }

    /**
     * @test
     */
    public function hasNoEnclosuresByDefault()
    {
        assertEmptyArray($this->rssFeedItem->getEnclosures());
    }

    /**
     * @test
     */
    public function canSetEnclosures()
    {
        assert(
                $this->rssFeedItem->deliveringEnclosure(
                        'http://stubbles.net/enclosure.mp3',
                        50,
                        'audio/mpeg'
                )->getEnclosures(),
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
        assertNull($this->rssFeedItem->getGuid());
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
        assert($this->rssFeedItem->withGuid('dummy')->getGuid(), equals('dummy'));
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
        assertNull($this->rssFeedItem->getPubDate());
    }

    /**
     * @test
     */
    public function publishingDateCanBePassedAsDateInstance()
    {
        $date = new Date('2008-05-24');
        assert(
                $this->rssFeedItem->publishedOn($date)->getPubDate(),
                equals('Sat 24 May 2008 00:00:00 ' . $date->offset())
        );
    }

    /**
     * @test
     */
    public function alternativePublishingDate()
    {
        $date = new Date('2008-05-24');
        assert(
                $this->rssFeedItem->publishedOn('2008-05-24')->getPubDate(),
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
        assertEmptyArray($this->rssFeedItem->getSources());
    }

    /**
     * @test
     */
    public function canSetSources()
    {
        assert(
                $this->rssFeedItem->inspiredBySource(
                        'stubbles',
                        'http://stubbles.net/source/'
                )->getSources(),
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
        assertEmptyString($this->rssFeedItem->getContent());
    }

    /**
     * @test
     */
    public function canSetContent()
    {
        assert(
                $this->rssFeedItem->withContent('<foo>bar</foo><baz/>')
                        ->getContent(),
                equals('<foo>bar</foo><baz/>')
        );
    }
}
