<?php
declare(strict_types=1);
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace stubbles\helper\rss\annotationbased;
/**
 * Helper class for the test.
 *
 * @RssFeedItem
 * @deprecated will be removed with 11.0.0
 */
class ExtendedRSSItemEntity extends SimpleRssItemEntity
{
    /**
     * returns the author
     */
    public function getAuthor(): string
    {
        return 'extendedAuthor';
    }

    /**
     * returns the categories
     */
    public function getCategories(): array
    {
        return [[
            'category' => 'extendedCategories',
            'domain'   => 'extendedDomain'
        ]];
    }

    /**
     * returns the comments url
     */
    public function getCommentsUrl(): string
    {
        return 'extendedCommentsUrl';
    }

    /**
     * returns the enclosures
     */
    public function getEnclosures(): array
    {
        return [[
            'url'    => 'extendedEnclosureUrl',
            'length' => 'extendedEnclosureLength',
            'type'   => 'extendedEnclosureType'
        ]];
    }

    /**
     * returns the guid
     */
    public function getGuid(): string
    {
        return 'extendedGuid';
    }

    /**
     * returns whether guid is perma link or not
     */
    public function isPermaLink(): bool
    {
        return false;
    }

    /**
     * returns the publishing date
     */
    public function getPubDate(): int
    {
        return 1221598221;
    }

    /**
     * returns the sources
     */
    public function getSources(): array
    {
        return [['name' => 'extendedSourceName', 'url' => 'extendedSourceUrl']];
    }

    /**
     * returns the content
     */
    public function getContent(): string
    {
        return 'extendedContent';
    }
}