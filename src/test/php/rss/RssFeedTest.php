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
        $this->assertEquals('test', $this->rssFeed->getTitle());
    }

    /**
     * @test
     */
    public function hasGivenLink()
    {
        $this->assertEquals('http://stubbles.net/', $this->rssFeed->getLink());
    }

    /**
     * @test
     */
    public function hasGivenDescription()
    {
        $this->assertEquals('description', $this->rssFeed->getDescription());
    }

    /**
     * @test
     */
    public function hasNoLocaleByDefault()
    {
        $this->assertFalse($this->rssFeed->hasLocale());
    }

    /**
     * @test
     */
    public function localeIsNullByDefault()
    {
        $this->assertNull($this->rssFeed->getLocale());
    }

    /**
     * @test
     */
    public function localeCanBeSet()
    {
        $this->assertEquals('en_EN',
                            $this->rssFeed->setLocale('en_EN')
                                          ->getLocale()
        );
    }

    /**
     * @test
     */
    public function hasNoCopyrightByDefault()
    {
        $this->assertFalse($this->rssFeed->hasCopyright());
    }

    /**
     * @test
     */
    public function copyrightIsNullByDefault()
    {
        $this->assertNull($this->rssFeed->getCopyright());
    }

    /**
     * @test
     */
    public function copyrightCanBeSet()
    {
        $this->assertEquals('(c) 2012 Stubbles',
                            $this->rssFeed->setCopyright('(c) 2012 Stubbles')
                                          ->getCopyright()
        );
    }

    /**
     * @test
     */
    public function hasNoManagingEditorByDefault()
    {
        $this->assertFalse($this->rssFeed->hasManagingEditor());
    }

    /**
     * @test
     */
    public function managingEditorIsNullByDefault()
    {
        $this->assertNull($this->rssFeed->getManagingEditor());
    }

    /**
     * @test
     */
    public function managingEditorSetWithoutMailAddress()
    {
        $this->assertEquals('nospam@example.com (mikey)',
                            $this->rssFeed->setManagingEditor('mikey')
                                          ->getManagingEditor()
        );
    }

    /**
     * @test
     */
    public function managingEditorSetWithMailAddress()
    {
        $this->assertEquals('test@example.com (mikey)',
                            $this->rssFeed->setManagingEditor('test@example.com (mikey)')
                                          ->getManagingEditor()
        );
    }

    /**
     * @test
     */
    public function hasNoStylesheetsByDefault()
    {
        $this->assertEquals([],
                            $this->rssFeed->getStylesheets()
        );
    }

    /**
     * @test
     */
    public function stylesheetsCanBeAdded()
    {
        $this->assertEquals(['foo.xsl', 'bar.xsl'],
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
        $this->assertFalse($this->rssFeed->hasWebMaster());
    }

    /**
     * @test
     */
    public function webmasterEditorIsNullByDefault()
    {
        $this->assertNull($this->rssFeed->getWebMaster());
    }

    /**
     * @test
     */
    public function webmasterEditorSetWithoutMailAddress()
    {
        $this->assertEquals('nospam@example.com (mikey)',
                            $this->rssFeed->setWebMaster('mikey')
                                          ->getWebMaster()
        );
    }

    /**
     * @test
     */
    public function webmasterEditorSetWithMailAddress()
    {
        $this->assertEquals('test@example.com (mikey)',
                            $this->rssFeed->setWebMaster('test@example.com (mikey)')
                                          ->getWebMaster()
        );
    }

    /**
     * @test
     */
    public function hasNoLastBuildDateByDefault()
    {
        $this->assertFalse($this->rssFeed->hasLastBuildDate());
    }

    /**
     * @test
     */
    public function initialLastBuildDateIsNull()
    {
        $this->assertNull($this->rssFeed->getLastBuildDate());
    }

    /**
     * @test
     */
    public function lastBuildDateCanBePassedAsDateInstance()
    {
        $date = new Date('2008-05-24');
        $this->assertEquals('Sat 24 May 2008 00:00:00 ' . $date->getOffset(),
                            $this->rssFeed->setLastBuildDate($date)
                                          ->getLastBuildDate()
        );
    }

    /**
     * @test
     */
    public function alternativeLastBuildDate()
    {
        $date = new Date('2008-05-24');
        $this->assertEquals('Sat 24 May 2008 00:00:00 ' . $date->getOffset(),
                            $this->rssFeed->setLastBuildDate('2008-05-24')
                                          ->getLastBuildDate()
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
        $this->assertFalse($this->rssFeed->hasTimeToLive());
    }

    /**
     * @test
     */
    public function timeToLiveIsNullByDefault()
    {
        $this->assertNull($this->rssFeed->getTimeToLive());
    }

    /**
     * @test
     */
    public function timeToLiveCanBeSet()
    {
        $this->assertEquals(303,
                            $this->rssFeed->setTimeToLive(303)
                                          ->getTimeToLive()
        );
    }

    /**
     * @test
     */
    public function hasNoImageByDefault()
    {
        $this->assertFalse($this->rssFeed->hasImage());
    }

    /**
     * @test
     */
    public function hasImageIfSet()
    {
        $this->assertTrue($this->rssFeed->setImage('http://example.com/foo.gif',
                                                   'image description'
                                          )
                                        ->hasImage()
        );
    }

    /**
     * @test
     */
    public function imageUrlIsEmptyByDefault()
    {
        $this->assertEquals('', $this->rssFeed->getImageUrl());
    }

    /**
     * @test
     */
    public function imageUrlCanBeSet()
    {
        $this->assertEquals('http://example.com/foo.gif',
                            $this->rssFeed->setImage('http://example.com/foo.gif',
                                                     'image description'
                                            )
                                          ->getImageUrl()
        );
    }

    /**
     * @test
     */
    public function imageDescriptionIsEmptyByDefault()
    {
        $this->assertEquals('', $this->rssFeed->getImageDescription());
    }

    /**
     * @test
     */
    public function imageDescriptionCanBeSet()
    {
        $this->assertEquals('image description',
                            $this->rssFeed->setImage('http://example.com/foo.gif',
                                                     'image description'
                                            )
                                          ->getImageDescription()
        );
    }

    /**
     * @test
     */
    public function imageWidthIs88ByDefault()
    {
        $this->assertEquals(88, $this->rssFeed->getImageWidth());
    }

    /**
     * @test
     */
    public function imageWidthIs88IfNotGiven()
    {
        $this->assertEquals(88,
                            $this->rssFeed->setImage('http://example.com/foo.gif',
                                                     'image description'
                                            )
                                          ->getImageWidth()
        );
    }

    /**
     * @test
     */
    public function imageWidthCanBeSet()
    {
        $this->assertEquals(100,
                            $this->rssFeed->setImage('http://example.com/foo.gif',
                                                     'image description',
                                                     100
                                            )
                                          ->getImageWidth()
        );
    }

    /**
     * @test
     */
    public function imageHeightIs31ByDefault()
    {
        $this->assertEquals(31, $this->rssFeed->getImageHeight());
    }

    /**
     * @test
     */
    public function imageHeightIs31IfNotGiven()
    {
        $this->assertEquals(31,
                            $this->rssFeed->setImage('http://example.com/foo.gif',
                                                     'image description'
                                            )
                                          ->getImageHeight()
        );
    }

    /**
     * @test
     */
    public function imageHeightCanBeSet()
    {
        $this->assertEquals(150,
                            $this->rssFeed->setImage('http://example.com/foo.gif',
                                                     'image description',
                                                     100,
                                                     150
                                            )
                                          ->getImageHeight()
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
        $this->assertEquals(0, $this->rssFeed->countItems());
        $this->assertEquals([], $this->rssFeed->getItems());
    }

    /**
     * @test
     */
    public function retrieveNonExistingItemReturnsNull()
    {
        $this->assertFalse($this->rssFeed->hasItem(0));
        $this->assertNull($this->rssFeed->getItem(0));
    }

    /**
     * @test
     */
    public function addedItemIsStored()
    {
        $item = $this->rssFeed->addItem('item', 'link', 'description');
        $this->assertEquals(1, $this->rssFeed->countItems());
        $this->assertEquals([$item], $this->rssFeed->getItems());
        $this->assertTrue($this->rssFeed->hasItem(0));
        $this->assertSame($item, $this->rssFeed->getItem(0));
    }

}
