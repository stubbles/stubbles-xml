<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  net\stubbles\xml
 */
namespace net\stubbles\xml\rss;
use net\stubbles\lang\BaseObject;
use net\stubbles\lang\types\Date;
/**
 * Helper class for the test.
 *
 * @RssFeedItem
 */
class MissingAllRssItemEntity extends BaseObject
{
    // intentionally empty
}
/**
 * Helper class for the test.
 *
 * @RssFeedItem
 */
class MissingLinkAndDescriptionRssItemEntity extends BaseObject
{
    /**
     * returns the title
     *
     * @return  string
     */
    public function getTitle()
    {
        return 'simpleTitle';
    }
}
/**
 * Helper class for the test.
 *
 * @RssFeedItem
 */
class MissingDescriptionRssItemEntity extends MissingLinkAndDescriptionRssItemEntity
{
    /**
     * returns the link
     *
     * @return  string
     */
    public function getLink()
    {
        return 'simpleLink';
    }
}
/**
 * Helper class for the test.
 *
 * @RssFeedItem
 */
class SimpleRssItemEntity extends MissingDescriptionRssItemEntity
{
    /**
     * returns the description
     *
     * @return  string
     */
    public function getDescription()
    {
        return 'simpleDescription';
    }
}
/**
 * Helper class for the test.
 *
 * @RssFeedItem
 */
class ExtendedRSSItemEntity extends SimpleRssItemEntity
{
    /**
     * returns the author
     *
     * @return  string
     */
    public function getAuthor()
    {
        return 'extendedAuthor';
    }

    /**
     * returns the categories
     *
     * @return  array
     */
    public function getCategories()
    {
        return array(array('category' => 'extendedCategories',
                           'domain'   => 'extendedDomain'
                     )
               );
    }

    /**
     * returns the comments url
     *
     * @return  string
     */
    public function getCommentsUrl()
    {
        return 'extendedCommentsUrl';
    }

    /**
     * returns the enclosures
     *
     * @return  array
     */
    public function getEnclosures()
    {
        return array(array('url'    => 'extendedEnclosureUrl',
                           'length' => 'extendedEnclosureLength',
                           'type'   => 'extendedEnclosureType'
                     )
               );
    }

    /**
     * returns the guid
     *
     * @return  string
     */
    public function getGuid()
    {
        return 'extendedGuid';
    }

    /**
     * returns whether guid is perma link or not
     *
     * @return  string
     */
    public function isPermaLink()
    {
        return false;
    }

    /**
     * returns the publishing date
     *
     * @return  string
     */
    public function getPubDate()
    {
        return 1221598221;
    }

    /**
     * returns the sources
     *
     * @return  array
     */
    public function getSources()
    {
        return array(array('name' => 'extendedSourceName', 'url' => 'extendedSourceUrl'));
    }

    /**
     * returns the content
     *
     * @return  string
     */
    public function getContent()
    {
        return 'extendedContent';
    }
}
/**
 * Helper class for the test.
 *
 * @RssFeedItem(titleMethod='getHeadline',
 *              linkMethod='getUrl',
 *              descriptionMethod='getTeaser',
 *              authorMethod='getCreator',
 *              categoriesMethod='getTags',
 *              getCommentsUrlMethod='getRemarks',
 *              enclosuresMethod='getImages',
 *              guidMethod='getId',
 *              isPermaLinkMethod='isPermanent',
 *              pubDateMethod='getDate',
 *              sourcesMethod='getOrigin',
 *              contentMethod='getText'
 * )
 */
class RssItemWithDifferentMethods
{
    /**
     * returns the title
     *
     * @return  string
     */
    public function getHeadline()
    {
        return 'headline';
    }

    /**
     * returns the link
     *
     * @return  string
     */
    public function getUrl()
    {
        return 'url';
    }

    /**
     * returns the description
     *
     * @return  string
     */
    public function getTeaser()
    {
        return 'teaser';
    }

    /**
     * returns the author
     *
     * @return  string
     */
    public function getCreator()
    {
        return 'creator@example.com (creator)';
    }

    /**
     * returns the categories
     *
     * @return  array
     */
    public function getTags()
    {
        return array(array('category' => 'tag1',
                           'domain'   => 'other'
                     )
               );
    }

    /**
     * returns the comments url
     *
     * @return  string
     */
    public function getRemarks()
    {
        return 'remarks';
    }

    /**
     * returns the enclosures
     *
     * @return  array
     */
    public function getImages()
    {
        return array(array('url'    => 'imagesUrl',
                           'length' => 'imagesLength',
                           'type'   => 'imagesType'
                     )
               );
    }

    /**
     * returns the guid
     *
     * @return  string
     */
    public function getId()
    {
        return 'id';
    }

    /**
     * returns whether guid is perma link or not
     *
     * @return  string
     */
    public function isPermanent()
    {
        return false;
    }

    /**
     * returns the publishing date
     *
     * @return  string
     */
    public function getDate()
    {
        return 1221598221;
    }

    /**
     * returns the sources
     *
     * @return  array
     */
    public function getOrigin()
    {
        return array(array('name' => 'originName', 'url' => 'originUrl'));
    }

    /**
     * returns the content
     *
     * @return  string
     */
    public function getText()
    {
        return 'text';
    }
}
/**
 * Test for net\stubbles\xml\rss\RssFeedItem::fromEntity().
 *
 * @group  xml
 * @group  xml_rss
 */
class RssFeedItemFromEntityTestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * instance to test
     *
     * @type  RssFeed
     */
    protected $rssFeed;

    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->rssFeed = new RssFeed('title', 'link', 'description');
    }

    /**
     * @test
     * @expectedException  net\stubbles\lang\exception\IllegalArgumentException
     */
    public function noObjectThrowsIllegalArgumentException()
    {
        $this->rssFeed->addEntity(313);
    }

    /**
     * @test
     * @expectedException  net\stubbles\xml\XmlException
     */
    public function missingAnnotationThrowsXmlException()
    {
        $this->rssFeed->addEntity(new \stdClass());
    }

    /**
     * @test
     * @expectedException  net\stubbles\xml\XmlException
     */
    public function missingTitleThrowsXmlException()
    {
        $this->rssFeed->addEntity(new MissingAllRssItemEntity());
    }

    /**
     * @test
     * @expectedException  net\stubbles\xml\XmlException
     */
    public function missingLinkThrowsXmlException()
    {
        $this->rssFeed->addEntity(new MissingLinkAndDescriptionRssItemEntity());
    }

    /**
     * @test
     * @expectedException  net\stubbles\xml\XmlException
     */
    public function missingDescriptionThrowsXmlException()
    {
        $this->rssFeed->addEntity(new MissingDescriptionRssItemEntity());
    }

    /**
     * simple entity is transformed into rss item
     *
     * @test
     */
    public function simpleEntity()
    {
        $rssFeedItem = $this->rssFeed->addEntity(new SimpleRssItemEntity());
        $this->assertEquals('simpleTitle', $rssFeedItem->getTitle());
        $this->assertEquals('simpleLink', $rssFeedItem->getLink());
        $this->assertEquals('simpleDescription', $rssFeedItem->getDescription());
        $this->assertNull($rssFeedItem->getAuthor());
        $this->assertEquals(array(), $rssFeedItem->getCategories());
        $this->assertNull($rssFeedItem->getComments());
        $this->assertEquals(array(), $rssFeedItem->getEnclosures());
        $this->assertNull($rssFeedItem->getGuid());
        $this->assertFalse($rssFeedItem->isGuidPermaLink());
        $this->assertNull($rssFeedItem->getPubDate());
        $this->assertEquals(array(), $rssFeedItem->getSources());
        $this->assertNull($rssFeedItem->getContent());
    }

    /**
     * simple entity is transformed into rss item using overrides
     *
     * @test
     */
    public function simpleEntityWithOverrides()
    {
        $rssFeedItem = $this->rssFeed->addEntity(new SimpleRssItemEntity(),
                                                          array('title'                 => 'overrideTitle',
                                                                'link'                  => 'overrideLink',
                                                                'description'           => 'overrideDescription',
                                                                'byAuthor'              => 'overrideAuthor',
                                                                'inCategories'          => array(array('category' => 'overrideCategories',
                                                                                                       'domain'   => 'overrideDomain'
                                                                                                 )
                                                                                           ),
                                                                'addCommentsAt'         => 'overrideCommentsUrl',
                                                                'deliveringEnclosures'  => array(array('url'   => 'overrideEnclosureUrl',
                                                                                                      'length' => 'overrideEnclosureLength',
                                                                                                      'type'   => 'overrideEnclosureType'
                                                                                                 )
                                                                                           ),
                                                                'withGuid'              => 'overrideGuid',
                                                                'andGuidIsNotPermaLink' => false,
                                                                'publishedOn'           => 1221598221,
                                                                'inspiredBySources'     => array(array('name' => 'overrideSourceName',
                                                                                                       'url'  => 'overrideSourceUrl'
                                                                                                 )
                                                                                           ),
                                                                'withContent'           => 'overrideContent'
                                                          )
                       );
        $this->assertEquals('overrideTitle', $rssFeedItem->getTitle());
        $this->assertEquals('overrideLink', $rssFeedItem->getLink());
        $this->assertEquals('overrideDescription', $rssFeedItem->getDescription());
        $this->assertEquals('nospam@example.com (overrideAuthor)', $rssFeedItem->getAuthor());
        $this->assertEquals(array(array('category' => 'overrideCategories',
                                        'domain'   => 'overrideDomain'
                                  )
                            ),
                            $rssFeedItem->getCategories()
        );
        $this->assertEquals('overrideCommentsUrl', $rssFeedItem->getComments());
        $this->assertEquals(array(array('url'    => 'overrideEnclosureUrl',
                                        'length' => 'overrideEnclosureLength',
                                        'type'   => 'overrideEnclosureType'
                                  )
                            ),
                            $rssFeedItem->getEnclosures()
        );
        $this->assertEquals('overrideGuid', $rssFeedItem->getGuid());
        $this->assertFalse($rssFeedItem->isGuidPermaLink());
        $date = new Date(1221598221);
        $this->assertEquals('Tue 16 Sep 2008 ' . $date->getHours() . ':50:21 ' . $date->getOffset(),
                            $rssFeedItem->getPubDate()
        );
        $this->assertEquals(array(array('name' => 'overrideSourceName', 'url' => 'overrideSourceUrl')),
                            $rssFeedItem->getSources()
        );
        $this->assertEquals('overrideContent', $rssFeedItem->getContent());
    }

    /**
     * extended entity is transformed into rss item
     *
     * @test
     */
    public function extendedEntity()
    {
        $rssFeedItem = $this->rssFeed->addEntity(new ExtendedRSSItemEntity());
        $this->assertEquals('simpleTitle', $rssFeedItem->getTitle());
        $this->assertEquals('simpleLink', $rssFeedItem->getLink());
        $this->assertEquals('simpleDescription', $rssFeedItem->getDescription());
        $this->assertEquals('nospam@example.com (extendedAuthor)', $rssFeedItem->getAuthor());
        $this->assertEquals(array(array('category' => 'extendedCategories',
                                        'domain'   => 'extendedDomain'
                                  )
                            ),
                            $rssFeedItem->getCategories()
        );
        $this->assertEquals('extendedCommentsUrl', $rssFeedItem->getComments());
        $this->assertEquals(array(array('url'    => 'extendedEnclosureUrl',
                                        'length' => 'extendedEnclosureLength',
                                        'type'   => 'extendedEnclosureType'
                                  )
                            ),
                            $rssFeedItem->getEnclosures()
        );
        $this->assertEquals('extendedGuid', $rssFeedItem->getGuid());
        $this->assertFalse($rssFeedItem->isGuidPermaLink());
        $date = new Date(1221598221);
        $this->assertEquals('Tue 16 Sep 2008 ' . $date->getHours() . ':50:21 ' . $date->getOffset(),
                            $rssFeedItem->getPubDate()
        );
        $this->assertEquals(array(array('name' => 'extendedSourceName', 'url' => 'extendedSourceUrl')),
                            $rssFeedItem->getSources()
        );
        $this->assertEquals('extendedContent', $rssFeedItem->getContent());
    }

    /**
     * different entity is transformed into rss item
     *
     * @test
     */
    public function differentEntity()
    {
        $rssFeedItem = $this->rssFeed->addEntity(new RssItemWithDifferentMethods());
        $this->assertEquals('headline', $rssFeedItem->getTitle());
        $this->assertEquals('url', $rssFeedItem->getLink());
        $this->assertEquals('teaser', $rssFeedItem->getDescription());
        $this->assertEquals('creator@example.com (creator)', $rssFeedItem->getAuthor());
        $this->assertEquals(array(array('category' => 'tag1',
                                        'domain'   => 'other'
                                  )
                            ),
                            $rssFeedItem->getCategories()
        );
        $this->assertEquals('remarks', $rssFeedItem->getComments());
        $this->assertEquals(array(array('url'    => 'imagesUrl',
                                        'length' => 'imagesLength',
                                        'type'   => 'imagesType'
                                  )
                            ),
                            $rssFeedItem->getEnclosures()
        );
        $this->assertEquals('id', $rssFeedItem->getGuid());
        $this->assertFalse($rssFeedItem->isGuidPermaLink());
        $date = new Date(1221598221);
        $this->assertEquals('Tue 16 Sep 2008 ' . $date->getHours() . ':50:21 ' . $date->getOffset(),
                            $rssFeedItem->getPubDate()
        );
        $this->assertEquals(array(array('name' => 'originName', 'url' => 'originUrl')),
                            $rssFeedItem->getSources()
        );
        $this->assertEquals('text', $rssFeedItem->getContent());
    }
}
?>