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
use stubbles\helper\rss\ExtendedRSSItemEntity;
use stubbles\helper\rss\MissingAllRssItemEntity;
use stubbles\helper\rss\MissingDescriptionRssItemEntity;
use stubbles\helper\rss\MissingLinkAndDescriptionRssItemEntity;
use stubbles\helper\rss\RssItemWithDifferentMethods;
use stubbles\helper\rss\SimpleRssItemEntity;
use stubbles\xml\XmlException;

use function bovigo\assert\assertThat;
use function bovigo\assert\assertEmptyArray;
use function bovigo\assert\assertFalse;
use function bovigo\assert\assertNull;
use function bovigo\assert\expect;
use function bovigo\assert\predicate\equals;
/**
 * Test for stubbles\xml\rss\RssFeedItem::fromEntity().
 *
 * @group  xml
 * @group  xml_rss
 */
class RssFeedItemFromEntityTest extends TestCase
{
    /**
     * instance to test
     *
     * @type  RssFeed
     */
    private $rssFeed;

    protected function setUp(): void
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
        assertThat(
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

        assertThat($rssFeedItem, equals($expectedRssFeedItem));
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
        assertThat(
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
        assertThat(
                $this->rssFeed->addEntity(new RssItemWithDifferentMethods()),
                equals($expectedRssFeedItem)
        );
    }
}
