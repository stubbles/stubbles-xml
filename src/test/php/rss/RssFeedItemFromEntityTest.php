<?php
declare(strict_types=1);
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace stubbles\xml\rss;

use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
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
use function bovigo\assert\expect;
use function bovigo\assert\predicate\equals;
/**
 * Test for stubbles\xml\rss\RssFeed->addEntity().
 */
#[Group('xml')]
#[Group('xml_rss')]
class RssFeedItemFromEntityTest extends TestCase
{
    private RssFeed $rssFeed;

    protected function setUp(): void
    {
        $this->rssFeed = new RssFeed('title', 'link', 'description');
    }

    #[Test]
    public function missingAttributeThrowsXmlException(): void
    {
        expect(function() { $this->rssFeed->addEntity(new \stdClass()); })
            ->throws(XmlException::class);
    }

    #[Test]
    public function missingTitleThrowsXmlException(): void
    {
        expect(function() { $this->rssFeed->addEntity(new MissingAllRssItemEntity());})
            ->throws(XmlException::class);
    }

    #[Test]
    public function missingLinkThrowsXmlException(): void
    {
        expect(function() { $this->rssFeed->addEntity(new MissingLinkAndDescriptionRssItemEntity());})
            ->throws(XmlException::class);
    }

    #[Test]
    public function missingDescriptionThrowsXmlException(): void
    {
        expect(function() { $this->rssFeed->addEntity(new MissingDescriptionRssItemEntity());})
            ->throws(XmlException::class);
    }

    #[Test]
    public function simpleEntityIsTransformedIntoRssItemWithMinimalProperties(): void
    {
        assertThat(
            $this->rssFeed->addEntity(new SimpleRssItemEntity()),
            equals(new RssFeedItem('simpleTitle', 'simpleLink', 'simpleDescription'))
        );
    }

    #[Test]
    public function simpleEntityWithOverrides(): void
    {
        $rssFeedItem = $this->rssFeed->addEntity(
                new SimpleRssItemEntity(),
                [
                    'title'                 => 'overrideTitle',
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

    #[Test]
    public function extendedEntity(): void
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

    #[Test]
    public function differentEntity(): void
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
