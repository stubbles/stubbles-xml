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
use stubbles\date\Date;
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
        assertEquals('test', $this->rssFeed->getTitle());
    }

    /**
     * @test
     */
    public function hasGivenLink()
    {
        assertEquals('http://stubbles.net/', $this->rssFeed->getLink());
    }

    /**
     * @test
     */
    public function hasGivenDescription()
    {
        assertEquals('description', $this->rssFeed->getDescription());
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
        assertEquals(
                'en_EN',
                $this->rssFeed->setLocale('en_EN')->getLocale()
        );
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
        assertEquals(
                '(c) 2012 Stubbles',
                $this->rssFeed->setCopyright('(c) 2012 Stubbles')
                        ->getCopyright()
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
        assertEquals(
                'nospam@example.com (mikey)',
                $this->rssFeed->setManagingEditor('mikey')->getManagingEditor()
        );
    }

    /**
     * @test
     */
    public function managingEditorSetWithMailAddress()
    {
        assertEquals(
                'test@example.com (mikey)',
                $this->rssFeed->setManagingEditor('test@example.com (mikey)')
                        ->getManagingEditor()
        );
    }

    /**
     * @test
     */
    public function hasNoStylesheetsByDefault()
    {
        assertEquals([], $this->rssFeed->getStylesheets());
    }

    /**
     * @test
     */
    public function stylesheetsCanBeAdded()
    {
        assertEquals(
                ['foo.xsl', 'bar.xsl'],
                $this->rssFeed->appendStylesheet('foo.xsl')
                        ->appendStylesheet('bar.xsl')
                        ->getStylesheets()
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
        assertEquals(
                'nospam@example.com (mikey)',
                $this->rssFeed->setWebMaster('mikey')->getWebMaster()
        );
    }

    /**
     * @test
     */
    public function webmasterEditorSetWithMailAddress()
    {
        assertEquals(
                'test@example.com (mikey)',
                $this->rssFeed->setWebMaster('test@example.com (mikey)')
                        ->getWebMaster()
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
        assertEquals(
                'Sat 24 May 2008 00:00:00 ' . $date->offset(),
                $this->rssFeed->setLastBuildDate($date)->getLastBuildDate()
        );
    }

    /**
     * @test
     */
    public function alternativeLastBuildDate()
    {
        $date = new Date('2008-05-24');
        assertEquals(
                'Sat 24 May 2008 00:00:00 ' . $date->offset(),
                $this->rssFeed->setLastBuildDate('2008-05-24')->getLastBuildDate()
        );
    }

    /**
     * @test
     * @expectedException  InvalidArgumentException
     */
    public function settingInvalidLastBuildDateThrowsIllegalArgumentException()
    {
        $this->rssFeed->setLastBuildDate('foo');
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
        assertEquals(
                303,
                $this->rssFeed->setTimeToLive(303)->getTimeToLive()
        );
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
        assertEquals('', $this->rssFeed->getImageUrl());
    }

    /**
     * @test
     */
    public function imageUrlCanBeSet()
    {
        assertEquals(
                'http://example.com/foo.gif',
                $this->rssFeed->setImage(
                        'http://example.com/foo.gif',
                        'image description'
                )->getImageUrl()
        );
    }

    /**
     * @test
     */
    public function imageDescriptionIsEmptyByDefault()
    {
        assertEquals('', $this->rssFeed->getImageDescription());
    }

    /**
     * @test
     */
    public function imageDescriptionCanBeSet()
    {
        assertEquals(
                'image description',
                $this->rssFeed->setImage(
                        'http://example.com/foo.gif',
                        'image description'
                )->getImageDescription()
        );
    }

    /**
     * @test
     */
    public function imageWidthIs88ByDefault()
    {
        assertEquals(88, $this->rssFeed->getImageWidth());
    }

    /**
     * @test
     */
    public function imageWidthIs88IfNotGiven()
    {
        assertEquals(
                88,
                $this->rssFeed->setImage(
                        'http://example.com/foo.gif',
                        'image description'
                )->getImageWidth()
        );
    }

    /**
     * @test
     */
    public function imageWidthCanBeSet()
    {
        assertEquals(
                100,
                $this->rssFeed->setImage(
                        'http://example.com/foo.gif',
                        'image description',
                        100
                )->getImageWidth()
        );
    }

    /**
     * @test
     */
    public function imageHeightIs31ByDefault()
    {
        assertEquals(31, $this->rssFeed->getImageHeight());
    }

    /**
     * @test
     */
    public function imageHeightIs31IfNotGiven()
    {
        assertEquals(
                31,
                $this->rssFeed->setImage(
                        'http://example.com/foo.gif',
                        'image description'
                )->getImageHeight()
        );
    }

    /**
     * @test
     */
    public function imageHeightCanBeSet()
    {
        assertEquals(
                150,
                $this->rssFeed->setImage(
                        'http://example.com/foo.gif',
                        'image description',
                        100,
                        150
                )->getImageHeight()
        );
    }

    /**
     * @test
     * @expectedException  InvalidArgumentException
     */
    public function imageWidthTooSmallThrowsIllegalArgumentException()
    {
        $this->rssFeed->setImage('http://example.org/example.gif', 'foo', -1);
    }

    /**
     * @test
     * @expectedException  InvalidArgumentException
     */
    public function imageWidthTooGreatThrowsIllegalArgumentException()
    {
        $this->rssFeed->setImage('http://example.org/example.gif', 'foo', 145);
    }

    /**
     * @test
     * @expectedException  InvalidArgumentException
     */
    public function imageHeightTooSmallThrowsIllegalArgumentException()
    {
         $this->rssFeed->setImage('http://example.org/example.gif', 'foo', 88, -1);
    }

    /**
     * @test
     * @expectedException  InvalidArgumentException
     */
    public function imageHeightTooGreatThrowsIllegalArgumentException()
    {
        $this->rssFeed->setImage('http://example.org/example.gif', 'foo', 88, 401);
    }

    /**
     * @test
     */
    public function hasNoItemsByDefault()
    {
        assertEquals(0, $this->rssFeed->countItems());
        assertEquals([], $this->rssFeed->getItems());
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
        assertEquals(1, $this->rssFeed->countItems());
        assertEquals([$item], $this->rssFeed->getItems());
        assertTrue($this->rssFeed->hasItem(0));
        assertSame($item, $this->rssFeed->getItem(0));
    }
}
