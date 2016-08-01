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
use function bovigo\assert\predicate\isSameAs;
/**
 * Test for stubbles\xml\rss\RssFeedGenerator.
 *
 * @group  xml
 * @group  xml_rss
 */
class RssFeedTest extends \PHPUnit_Framework_TestCase
{
    /**
     * instance to test
     *
     * @type  RssFeed
     */
    private $rssFeed;

    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->rssFeed = new RssFeed('test', 'http://stubbles.net/', 'description');
    }

    /**
     * @test
     */
    public function hasGivenTitle()
    {
        assert($this->rssFeed->getTitle(), equals('test'));
    }

    /**
     * @test
     */
    public function hasGivenLink()
    {
        assert($this->rssFeed->getLink(), equals('http://stubbles.net/'));
    }

    /**
     * @test
     */
    public function hasGivenDescription()
    {
        assert($this->rssFeed->getDescription(), equals('description'));
    }

    /**
     * @test
     */
    public function hasNoLocaleByDefault()
    {
        assertFalse($this->rssFeed->hasLocale());
    }

    /**
     * @test
     */
    public function localeIsNullByDefault()
    {
        assertNull($this->rssFeed->getLocale());
    }

    /**
     * @test
     */
    public function localeCanBeSet()
    {
        assert($this->rssFeed->setLocale('en_EN')->getLocale(), equals('en_EN'));
    }

    /**
     * @test
     */
    public function hasNoCopyrightByDefault()
    {
        assertFalse($this->rssFeed->hasCopyright());
    }

    /**
     * @test
     */
    public function copyrightIsNullByDefault()
    {
        assertNull($this->rssFeed->getCopyright());
    }

    /**
     * @test
     */
    public function copyrightCanBeSet()
    {
        assert(
                $this->rssFeed->setCopyright('(c) 2012 Stubbles')
                        ->getCopyright(),
                equals('(c) 2012 Stubbles')
        );
    }

    /**
     * @test
     */
    public function hasNoManagingEditorByDefault()
    {
        assertFalse($this->rssFeed->hasManagingEditor());
    }

    /**
     * @test
     */
    public function managingEditorIsNullByDefault()
    {
        assertNull($this->rssFeed->getManagingEditor());
    }

    /**
     * @test
     */
    public function managingEditorSetWithoutMailAddress()
    {
        assert(
                $this->rssFeed->setManagingEditor('mikey')->getManagingEditor(),
                equals('nospam@example.com (mikey)')
        );
    }

    /**
     * @test
     */
    public function managingEditorSetWithMailAddress()
    {
        assert(
                $this->rssFeed->setManagingEditor('test@example.com (mikey)')
                        ->getManagingEditor(),
                equals('test@example.com (mikey)')
        );
    }

    /**
     * @test
     */
    public function hasNoStylesheetsByDefault()
    {
        assertEmptyArray($this->rssFeed->getStylesheets());
    }

    /**
     * @test
     */
    public function stylesheetsCanBeAdded()
    {
        assert(
                $this->rssFeed->appendStylesheet('foo.xsl')
                        ->appendStylesheet('bar.xsl')
                        ->getStylesheets(),
                equals(['foo.xsl', 'bar.xsl'])
        );
    }

    /**
     * @test
     */
    public function hasNoWebmasterByDefault()
    {
        assertFalse($this->rssFeed->hasWebMaster());
    }

    /**
     * @test
     */
    public function webmasterEditorIsNullByDefault()
    {
        assertNull($this->rssFeed->getWebMaster());
    }

    /**
     * @test
     */
    public function webmasterEditorSetWithoutMailAddress()
    {
        assert(
                $this->rssFeed->setWebMaster('mikey')->getWebMaster(),
                equals('nospam@example.com (mikey)')
        );
    }

    /**
     * @test
     */
    public function webmasterEditorSetWithMailAddress()
    {
        assert(
                $this->rssFeed->setWebMaster('test@example.com (mikey)')
                        ->getWebMaster(),
                equals('test@example.com (mikey)')
        );
    }

    /**
     * @test
     */
    public function hasNoLastBuildDateByDefault()
    {
        assertFalse($this->rssFeed->hasLastBuildDate());
    }

    /**
     * @test
     */
    public function initialLastBuildDateIsNull()
    {
        assertNull($this->rssFeed->getLastBuildDate());
    }

    /**
     * @test
     */
    public function lastBuildDateCanBePassedAsDateInstance()
    {
        $date = new Date('2008-05-24');
        assert(
                $this->rssFeed->setLastBuildDate($date)->getLastBuildDate(),
                equals('Sat 24 May 2008 00:00:00 ' . $date->offset())
        );
    }

    /**
     * @test
     */
    public function alternativeLastBuildDate()
    {
        $date = new Date('2008-05-24');
        assert(
                $this->rssFeed->setLastBuildDate('2008-05-24')->getLastBuildDate(),
                equals('Sat 24 May 2008 00:00:00 ' . $date->offset())
        );
    }

    /**
     * @test
     */
    public function settingInvalidLastBuildDateThrowsIllegalArgumentException()
    {
        expect(function() { $this->rssFeed->setLastBuildDate('foo'); })
                ->throws(\InvalidArgumentException::class);
    }

    /**
     * @test
     */
    public function hasNoTimeToLiveByDefault()
    {
        assertFalse($this->rssFeed->hasTimeToLive());
    }

    /**
     * @test
     */
    public function timeToLiveIsNullByDefault()
    {
        assertNull($this->rssFeed->getTimeToLive());
    }

    /**
     * @test
     */
    public function timeToLiveCanBeSet()
    {
        assert($this->rssFeed->setTimeToLive(303)->getTimeToLive(), equals(303));
    }

    /**
     * @test
     */
    public function hasNoImageByDefault()
    {
        assertFalse($this->rssFeed->hasImage());
    }

    /**
     * @test
     */
    public function hasImageIfSet()
    {
        assertTrue(
                $this->rssFeed->setImage(
                        'http://example.com/foo.gif',
                        'image description'
                )->hasImage()
        );
    }

    /**
     * @test
     */
    public function imageUrlIsEmptyByDefault()
    {
        assertEmptyString($this->rssFeed->getImageUrl());
    }

    /**
     * @test
     */
    public function imageUrlCanBeSet()
    {
        assert(
                $this->rssFeed->setImage(
                        'http://example.com/foo.gif',
                        'image description'
                )->getImageUrl(),
                equals('http://example.com/foo.gif')
        );
    }

    /**
     * @test
     */
    public function imageDescriptionIsEmptyByDefault()
    {
        assertEmptyString($this->rssFeed->getImageDescription());
    }

    /**
     * @test
     */
    public function imageDescriptionCanBeSet()
    {
        assert(
                $this->rssFeed->setImage(
                        'http://example.com/foo.gif',
                        'image description'
                )->getImageDescription(),
                equals('image description')
        );
    }

    /**
     * @test
     */
    public function imageWidthIs88ByDefault()
    {
        assert($this->rssFeed->getImageWidth(), equals(88));
    }

    /**
     * @test
     */
    public function imageWidthIs88IfNotGiven()
    {
        assert(
                $this->rssFeed->setImage(
                        'http://example.com/foo.gif',
                        'image description'
                )->getImageWidth(),
                equals(88)
        );
    }

    /**
     * @test
     */
    public function imageWidthCanBeSet()
    {
        assert(
                $this->rssFeed->setImage(
                        'http://example.com/foo.gif',
                        'image description',
                        100
                )->getImageWidth(),
                equals(100)
        );
    }

    /**
     * @test
     */
    public function imageHeightIs31ByDefault()
    {
        assert($this->rssFeed->getImageHeight(), equals(31));
    }

    /**
     * @test
     */
    public function imageHeightIs31IfNotGiven()
    {
        assert(
                $this->rssFeed->setImage(
                        'http://example.com/foo.gif',
                        'image description'
                )->getImageHeight(),
                equals(31)
        );
    }

    /**
     * @test
     */
    public function imageHeightCanBeSet()
    {
        assert(
                $this->rssFeed->setImage(
                        'http://example.com/foo.gif',
                        'image description',
                        100,
                        150
                )->getImageHeight(),
                equals(150)
        );
    }

    /**
     * @test
     */
    public function imageWidthTooSmallThrowsIllegalArgumentException()
    {
        expect(function() {
                $this->rssFeed->setImage('http://example.org/example.gif', 'foo', -1);
        })->throws(\InvalidArgumentException::class);
    }

    /**
     * @test
     */
    public function imageWidthTooGreatThrowsIllegalArgumentException()
    {
        expect(function() {
                $this->rssFeed->setImage('http://example.org/example.gif', 'foo', 145);
        })->throws(\InvalidArgumentException::class);
    }

    /**
     * @test
     */
    public function imageHeightTooSmallThrowsIllegalArgumentException()
    {
        expect(function() {
                $this->rssFeed->setImage('http://example.org/example.gif', 'foo', 88, -1);
        })->throws(\InvalidArgumentException::class);
    }

    /**
     * @test
     */
    public function imageHeightTooGreatThrowsIllegalArgumentException()
    {
        expect(function() {
                $this->rssFeed->setImage('http://example.org/example.gif', 'foo', 88, 401);
        })->throws(\InvalidArgumentException::class);
    }

    /**
     * @test
     */
    public function hasNoItemsByDefault()
    {
        assert($this->rssFeed->countItems(), equals(0));
        assertEmptyArray($this->rssFeed->getItems());
    }

    /**
     * @test
     */
    public function retrieveNonExistingItemReturnsNull()
    {
        assertFalse($this->rssFeed->hasItem(0));
        assertNull($this->rssFeed->getItem(0));
    }

    /**
     * @test
     */
    public function addedItemIsStored()
    {
        $item = $this->rssFeed->addItem('item', 'link', 'description');
        assert($this->rssFeed->countItems(), equals(1));
        assert($this->rssFeed->getItems(), equals([$item]));
        assertTrue($this->rssFeed->hasItem(0));
        assert($this->rssFeed->getItem(0), isSameAs($item));
    }
}
