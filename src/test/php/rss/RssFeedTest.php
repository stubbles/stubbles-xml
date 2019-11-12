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

use function bovigo\assert\{
    assertThat,
    assertEmptyArray,
    assertEmptyString,
    assertFalse,
    assertNull,
    assertTrue,
    expect,
    predicate\equals,
    predicate\isSameAs
};
/**
 * Test for stubbles\xml\rss\RssFeedGenerator.
 *
 * @group  xml
 * @group  xml_rss
 */
class RssFeedTest extends TestCase
{
    /**
     * instance to test
     *
     * @type  RssFeed
     */
    private $rssFeed;

    protected function setUp(): void
    {
        $this->rssFeed = new RssFeed('test', 'http://stubbles.net/', 'description');
    }

    /**
     * @test
     */
    public function hasGivenTitle()
    {
        assertThat($this->rssFeed->title(), equals('test'));
    }

    /**
     * @test
     */
    public function hasGivenLink()
    {
        assertThat($this->rssFeed->link(), equals('http://stubbles.net/'));
    }

    /**
     * @test
     */
    public function hasGivenDescription()
    {
        assertThat($this->rssFeed->description(), equals('description'));
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
        assertNull($this->rssFeed->locale());
    }

    /**
     * @test
     */
    public function localeCanBeSet()
    {
        assertThat($this->rssFeed->setLocale('en_EN')->locale(), equals('en_EN'));
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
        assertNull($this->rssFeed->copyright());
    }

    /**
     * @test
     */
    public function copyrightCanBeSet()
    {
        assertThat(
                $this->rssFeed->setCopyright('(c) 2012 Stubbles')->copyright(),
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
        assertNull($this->rssFeed->managingEditor());
    }

    /**
     * @test
     */
    public function managingEditorSetWithoutMailAddress()
    {
        assertThat(
                $this->rssFeed->setManagingEditor('mikey')->managingEditor(),
                equals('nospam@example.com (mikey)')
        );
    }

    /**
     * @test
     */
    public function managingEditorSetWithMailAddress()
    {
        assertThat(
                $this->rssFeed->setManagingEditor('test@example.com (mikey)')
                        ->managingEditor(),
                equals('test@example.com (mikey)')
        );
    }

    /**
     * @test
     */
    public function hasNoStylesheetsByDefault()
    {
        assertEmptyArray($this->rssFeed->stylesheets());
    }

    /**
     * @test
     */
    public function stylesheetsCanBeAdded()
    {
        assertThat(
                $this->rssFeed->appendStylesheet('foo.xsl')
                        ->appendStylesheet('bar.xsl')
                        ->stylesheets(),
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
        assertNull($this->rssFeed->webMaster());
    }

    /**
     * @test
     */
    public function webmasterEditorSetWithoutMailAddress()
    {
        assertThat(
                $this->rssFeed->setWebMaster('mikey')->webMaster(),
                equals('nospam@example.com (mikey)')
        );
    }

    /**
     * @test
     */
    public function webmasterEditorSetWithMailAddress()
    {
        assertThat(
                $this->rssFeed->setWebMaster('test@example.com (mikey)')
                        ->webMaster(),
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
        assertNull($this->rssFeed->lastBuildDate());
    }

    /**
     * @test
     */
    public function lastBuildDateCanBePassedAsDateInstance()
    {
        $date = new Date('2008-05-24');
        assertThat(
                $this->rssFeed->setLastBuildDate($date)->lastBuildDate(),
                equals('Sat 24 May 2008 00:00:00 ' . $date->offset())
        );
    }

    /**
     * @test
     */
    public function alternativeLastBuildDate()
    {
        $date = new Date('2008-05-24');
        assertThat(
                $this->rssFeed->setLastBuildDate('2008-05-24')->lastBuildDate(),
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
        assertNull($this->rssFeed->timeToLive());
    }

    /**
     * @test
     */
    public function timeToLiveCanBeSet()
    {
        assertThat($this->rssFeed->setTimeToLive(303)->timeToLive(), equals(303));
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
        assertEmptyString($this->rssFeed->imageUrl());
    }

    /**
     * @test
     */
    public function imageUrlCanBeSet()
    {
        assertThat(
                $this->rssFeed->setImage(
                        'http://example.com/foo.gif',
                        'image description'
                )->imageUrl(),
                equals('http://example.com/foo.gif')
        );
    }

    /**
     * @test
     */
    public function imageDescriptionIsEmptyByDefault()
    {
        assertEmptyString($this->rssFeed->imageDescription());
    }

    /**
     * @test
     */
    public function imageDescriptionCanBeSet()
    {
        assertThat(
                $this->rssFeed->setImage(
                        'http://example.com/foo.gif',
                        'image description'
                )->imageDescription(),
                equals('image description')
        );
    }

    /**
     * @test
     */
    public function imageWidthIs88ByDefault()
    {
        assertThat($this->rssFeed->imageWidth(), equals(88));
    }

    /**
     * @test
     */
    public function imageWidthIs88IfNotGiven()
    {
        assertThat(
                $this->rssFeed->setImage(
                        'http://example.com/foo.gif',
                        'image description'
                )->imageWidth(),
                equals(88)
        );
    }

    /**
     * @test
     */
    public function imageWidthCanBeSet()
    {
        assertThat(
                $this->rssFeed->setImage(
                        'http://example.com/foo.gif',
                        'image description',
                        100
                )->imageWidth(),
                equals(100)
        );
    }

    /**
     * @test
     */
    public function imageHeightIs31ByDefault()
    {
        assertThat($this->rssFeed->imageHeight(), equals(31));
    }

    /**
     * @test
     */
    public function imageHeightIs31IfNotGiven()
    {
        assertThat(
                $this->rssFeed->setImage(
                        'http://example.com/foo.gif',
                        'image description'
                )->imageHeight(),
                equals(31)
        );
    }

    /**
     * @test
     */
    public function imageHeightCanBeSet()
    {
        assertThat(
                $this->rssFeed->setImage(
                        'http://example.com/foo.gif',
                        'image description',
                        100,
                        150
                )->imageHeight(),
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
        assertThat($this->rssFeed->countItems(), equals(0));
        assertEmptyArray($this->rssFeed->items());
    }

    /**
     * @test
     */
    public function retrieveNonExistingItemReturnsNull()
    {
        assertFalse($this->rssFeed->hasItem(0));
        assertNull($this->rssFeed->item(0));
    }

    /**
     * @test
     */
    public function addedItemIsStored()
    {
        $item = $this->rssFeed->addItem('item', 'link', 'description');
        assertThat($this->rssFeed->countItems(), equals(1));
        assertThat($this->rssFeed->items(), equals([$item]));
        assertTrue($this->rssFeed->hasItem(0));
        assertThat($this->rssFeed->item(0), isSameAs($item));
    }
}
