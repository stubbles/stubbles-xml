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
 * Test for stubbles\xml\rss\RssFeedItem.
 *
 * @group  xml
 * @group  xml_rss
 */
class RssFeedItemTestCase extends \PHPUnit_Framework_TestCase
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
        $this->rssFeedItem = RssFeedItem::create('test',
                                                 'http://stubbles.net/',
                                                 'description'
                             );
    }

    /**
     * @test
     */
    public function hasGivenTitleByDefault()
    {
        $this->assertEquals('test', $this->rssFeedItem->getTitle());
    }

    /**
     * @test
     */
    public function hasGivenLinkByDefault()
    {
        $this->assertEquals('http://stubbles.net/', $this->rssFeedItem->getLink());
    }

    /**
     * @test
     */
    public function hasGivenDescriptionByDefault()
    {
        $this->assertEquals('description', $this->rssFeedItem->getDescription());
    }

    /**
     * @test
     */
    public function hasNoAuthorByDefault()
    {
        $this->assertFalse($this->rssFeedItem->hasAuthor());
    }

    /**
     * @test
     */
    public function initialAuthorIsNull()
    {
        $this->assertNull($this->rssFeedItem->getAuthor());
    }

    /**
     * @test
     */
    public function setAuthorWithoutMailAddressUsesExampleMailAddress()
    {
        $this->assertEquals('nospam@example.com (mikey)',
                            $this->rssFeedItem->byAuthor('mikey')
                                              ->getAuthor()
        );
    }

    /**
     * @test
     */
    public function canSetAuthorWithMailAddress()
    {
        $this->assertEquals('test@example.net (mikey)',
                            $this->rssFeedItem->byAuthor('test@example.net (mikey)')
                                              ->getAuthor()
        );
    }

    /**
     * @test
     */
    public function hasNoCategoriesByDefault()
    {
        $this->assertEquals([], $this->rssFeedItem->getCategories());
    }

    /**
     * @test
     */
    public function canSetCategories()
    {
        $this->assertEquals([['category' => 'cat1',
                              'domain'   => ''
                             ],
                             ['category' => 'cat2',
                              'domain'   => 'domain'
                             ]
                            ],
                            $this->rssFeedItem->inCategory('cat1')
                                              ->inCategory('cat2', 'domain')
                                              ->getCategories()
        );
    }

    /**
     * @test
     */
    public function canSetListOfCategories()
    {
        $this->assertEquals([['category' => 'cat1',
                              'domain'   => ''
                             ],
                             ['category' => 'cat2',
                              'domain'   => ''
                             ]
                            ],
                            $this->rssFeedItem->inCategories(['cat1', 'cat2'])
                                              ->getCategories()
        );
    }

    /**
     * @test
     */
    public function hasNoCommentsUrlByDefault()
    {
        $this->assertFalse($this->rssFeedItem->hasComments());
    }

    /**
     * @test
     */
    public function initialCommentUrlIsNull()
    {
        $this->assertNull($this->rssFeedItem->getComments());
    }

    /**
     * @test
     */
    public function canSetCommentsUrl()
    {
        $this->assertEquals('http://stubbles.net/comments/',
                            $this->rssFeedItem->addCommentsAt('http://stubbles.net/comments/')
                                              ->getComments()
        );
    }

    /**
     * @test
     */
    public function hasNoEnclosuresByDefault()
    {
        $this->assertEquals([], $this->rssFeedItem->getEnclosures());
    }

    /**
     * @test
     */
    public function canSetEnclosures()
    {
        $this->assertEquals([['url'    => 'http://stubbles.net/enclosure.mp3',
                              'length' => 50,
                              'type' => 'audio/mpeg'
                             ]
                            ],
                            $this->rssFeedItem->deliveringEnclosure('http://stubbles.net/enclosure.mp3',
                                                                    50,
                                                                    'audio/mpeg'
                                                )
                                              ->getEnclosures()
        );
    }

    /**
     * @test
     */
    public function hasNoGuidByDefault()
    {
        $this->assertFalse($this->rssFeedItem->hasGuid());
    }

    /**
     * @test
     */
    public function initialGuidIsNull()
    {
        $this->assertNull($this->rssFeedItem->getGuid());
    }

    /**
     * @test
     */
    public function guidIsNotPermalinkByDefault()
    {
        $this->assertFalse($this->rssFeedItem->isGuidPermaLink());
    }

    /**
     * @test
     */
    public function canSetGuid()
    {
        $this->assertEquals('dummy',
                            $this->rssFeedItem->withGuid('dummy')
                                              ->getGuid()
        );
    }

    /**
     * @test
     */
    public function settingGuidEnablesGuidAsPermalink()
    {
        $this->assertTrue($this->rssFeedItem->withGuid('dummy')
                                            ->isGuidPermaLink()
        );
    }

    /**
     * @test
     */
    public function settingGuidAndDisablingPermalink()
    {
        $this->assertFalse($this->rssFeedItem->withGuid('dummy')
                                             ->andGuidIsNotPermaLink()
                                             ->isGuidPermaLink()
        );
    }

    /**
     * @test
     */
    public function hasNoPublishingDateByDefault()
    {
        $this->assertFalse($this->rssFeedItem->hasPubDate());
    }

    /**
     * @test
     */
    public function initialPublishingDateIsNull()
    {
        $this->assertNull($this->rssFeedItem->getPubDate());
    }

    /**
     * @test
     */
    public function publishingDateCanBePassedAsDateInstance()
    {
        $date = new Date('2008-05-24');
        $this->assertEquals('Sat 24 May 2008 00:00:00 ' . $date->getOffset(),
                            $this->rssFeedItem->publishedOn($date)
                                              ->getPubDate()
        );
    }

    /**
     * @test
     */
    public function alternativePublishingDate()
    {
        $date = new Date('2008-05-24');
        $this->assertEquals('Sat 24 May 2008 00:00:00 ' . $date->getOffset(),
                            $this->rssFeedItem->publishedOn('2008-05-24')
                                              ->getPubDate()
        );
    }

    /**
     * @test
     * @expectedException  stubbles\lang\exception\IllegalArgumentException
     */
    public function settingInvalidPublishingDateThrowsIllegalArgumentException()
    {
        $this->rssFeedItem->publishedOn('foo');
    }

    /**
     * @test
     */
    public function hasNoSourcesByDefault()
    {
        $this->assertEquals([], $this->rssFeedItem->getSources());
    }

    /**
     * @test
     */
    public function canSetSources()
    {
        $this->assertEquals([['name' => 'stubbles',
                              'url'  => 'http://stubbles.net/source/'
                             ]
                            ],
                            $this->rssFeedItem->inspiredBySource('stubbles',
                                                                 'http://stubbles.net/source/'
                                                )
                                              ->getSources()
        );
    }

    /**
     * @test
     */
    public function hasNoContentByDefault()
    {
        $this->assertFalse($this->rssFeedItem->hasContent());
    }

    /**
     * @test
     */
    public function initialContentIsEmpty()
    {
        $this->assertEquals('', $this->rssFeedItem->getContent());
    }

    /**
     * @test
     */
    public function canSetContent()
    {
        $this->assertEquals('<foo>bar</foo><baz/>',
                            $this->rssFeedItem->withContent('<foo>bar</foo><baz/>')
                                              ->getContent()
        );
    }
}
