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
use stubbles\xml\XmlException;

use function bovigo\assert\assert;
use function bovigo\assert\assertEmptyArray;
use function bovigo\assert\assertFalse;
use function bovigo\assert\assertNull;
use function bovigo\assert\expect;
use function bovigo\assert\predicate\equals;
/**
 * Helper class for the test.
 *
 * @RssFeedItem
 */
class MissingAllRssItemEntity
{
    // intentionally empty
}
/**
 * Helper class for the test.
 *
 * @RssFeedItem
 */
class MissingLinkAndDescriptionRssItemEntity
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
        return [[
                'category' => 'extendedCategories',
                'domain'   => 'extendedDomain'
        ]];
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
        return [[
                'url'    => 'extendedEnclosureUrl',
                'length' => 'extendedEnclosureLength',
                'type'   => 'extendedEnclosureType'
        ]];
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
        return [['name' => 'extendedSourceName', 'url' => 'extendedSourceUrl']];
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
        return [['category' => 'tag1', 'domain'   => 'other']];
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
        return [[
                'url'    => 'imagesUrl',
                'length' => 'imagesLength',
                'type'   => 'imagesType'
        ]];
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
        return [['name' => 'originName', 'url' => 'originUrl']];
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
 * Test for stubbles\xml\rss\RssFeedItem::fromEntity().
 *
 * @group  xml
 * @group  xml_rss
 */
class RssFeedItemFromEntityTest extends \PHPUnit_Framework_TestCase
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
        $this->rssFeed = new RssFeed('title', 'link', 'description');
    }

    /**
     * @test
     */
    public function noObjectThrowsIllegalArgumentException()
    {
        expect(function() { $this->rssFeed->addEntity(313); })
                ->throws(\InvalidArgumentException::class);
    }

    /**
     * @test
     */
    public function missingAnnotationThrowsXmlException()
    {
        expect(function() { $this->rssFeed->addEntity(new \stdClass()); })
                ->throws(XmlException::class);
    }

    /**
     * @test
     */
    public function missingTitleThrowsXmlException()
    {
        expect(function() { $this->rssFeed->addEntity(new MissingAllRssItemEntity());})
                ->throws(XmlException::class);
    }

    /**
     * @test
     */
    public function missingLinkThrowsXmlException()
    {
        expect(function() { $this->rssFeed->addEntity(new MissingLinkAndDescriptionRssItemEntity());})
                ->throws(XmlException::class);
    }

    /**
     * @test
     */
    public function missingDescriptionThrowsXmlException()
    {
        expect(function() { $this->rssFeed->addEntity(new MissingDescriptionRssItemEntity());})
                ->throws(XmlException::class);
    }

    /**
     * @test
     */
    public function simpleEntityIsTransformedIntoRssItemWithMinimalProperties()
    {
        assert(
                $this->rssFeed->addEntity(new SimpleRssItemEntity()),
                equals(new RssFeedItem('simpleTitle', 'simpleLink', 'simpleDescription'))
        );
    }

    /**
     * @test
     */
    public function simpleEntityWithOverrides()
    {
        $rssFeedItem = $this->rssFeed->addEntity(
                new SimpleRssItemEntity(),
                ['title'                 => 'overrideTitle',
                 'link'                  => 'overrideLink',
                 'description'           => 'overrideDescription',
                 'byAuthor'              => 'overrideAuthor',
                 'inCategories'          => [['category' => 'overrideCategories',
                                              'domain'   => 'overrideDomain'
                                             ]
                                            ],
                 'addCommentsAt'         => 'overrideCommentsUrl',
                 'deliveringEnclosures'  => [['url'   => 'overrideEnclosureUrl',
                                              'length' => 'overrideEnclosureLength',
                                              'type'   => 'overrideEnclosureType'
                                             ]
                                            ],
                 'withGuid'              => 'overrideGuid',
                 'andGuidIsNotPermaLink' => false,
                 'publishedOn'           => 1221598221,
                 'inspiredBySources'     => [['name' => 'overrideSourceName',
                                              'url'  => 'overrideSourceUrl'
                                             ]
                                            ],
                 'withContent'           => 'overrideContent'
                ]
        );

        $expectedRssFeedItem = (new RssFeedItem(
                'overrideTitle',
                'overrideLink',
                'overrideDescription'
        ))->byAuthor('nospam@example.com (overrideAuthor)')
            ->inCategories([[
                    'category' => 'overrideCategories',
                    'domain'   => 'overrideDomain'
            ]])
            ->addCommentsAt('overrideCommentsUrl')
            ->deliveringEnclosures([[
                    'url'    => 'overrideEnclosureUrl',
                    'length' => 'overrideEnclosureLength',
                    'type'   => 'overrideEnclosureType'
            ]])
            ->withGuid('overrideGuid')
            ->andGuidIsNotPermaLink()
            ->publishedOn(new Date(1221598221))
            ->inspiredBySources([[
                    'name' => 'overrideSourceName', 'url' => 'overrideSourceUrl'
            ]])
            ->withContent('overrideContent');

        assert($rssFeedItem, equals($expectedRssFeedItem));
    }

    /**
     * @test
     */
    public function extendedEntity()
    {
        $expectedRssFeedItem = (new RssFeedItem(
                'simpleTitle',
                'simpleLink',
                'simpleDescription'
        ))->byAuthor('nospam@example.com (extendedAuthor)')
            ->inCategories([[
                    'category' => 'extendedCategories',
                    'domain'   => 'extendedDomain'
            ]])
            ->addCommentsAt('extendedCommentsUrl')
            ->deliveringEnclosures([[
                    'url'    => 'extendedEnclosureUrl',
                    'length' => 'extendedEnclosureLength',
                    'type'   => 'extendedEnclosureType'
            ]])
            ->withGuid('extendedGuid')
            ->andGuidIsNotPermaLink()
            ->publishedOn(new Date(1221598221))
            ->inspiredBySources([[
                    'name' => 'extendedSourceName', 'url' => 'extendedSourceUrl'
            ]])
            ->withContent('extendedContent');
        assert(
                $this->rssFeed->addEntity(new ExtendedRSSItemEntity()),
                equals($expectedRssFeedItem)
        );
    }

    /**
     * @test
     */
    public function differentEntity()
    {
        $expectedRssFeedItem = (new RssFeedItem('headline', 'url', 'teaser'))
            ->byAuthor('creator@example.com (creator)')
            ->inCategories([[
                    'category' => 'tag1',
                    'domain'   => 'other'
            ]])
            ->addCommentsAt('remarks')
            ->deliveringEnclosures([[
                    'url'    => 'imagesUrl',
                    'length' => 'imagesLength',
                    'type'   => 'imagesType'
            ]])
            ->withGuid('id')
            ->andGuidIsNotPermaLink()
            ->publishedOn(new Date(1221598221))
            ->inspiredBySources([[
                    'name' => 'originName', 'url' => 'originUrl'
            ]])
            ->withContent('text');
        assert(
                $this->rssFeed->addEntity(new RssItemWithDifferentMethods()),
                equals($expectedRssFeedItem)
        );
    }
}
