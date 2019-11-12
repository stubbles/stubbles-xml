<?php
declare(strict_types=1);
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace stubbles\helper\rss;
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
    public function getAuthor(): string
    {
        return 'extendedAuthor';
    }

    /**
     * returns the categories
     *
     * @return  array
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
     *
     * @return  string
     */
    public function getCommentsUrl(): string
    {
        return 'extendedCommentsUrl';
    }

    /**
     * returns the enclosures
     *
     * @return  array
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
     *
     * @return  string
     */
    public function getGuid(): string
    {
        return 'extendedGuid';
    }

    /**
     * returns whether guid is perma link or not
     *
     * @return  bool
     */
    public function isPermaLink(): bool
    {
        return false;
    }

    /**
     * returns the publishing date
     *
     * @return  int
     */
    public function getPubDate(): int
    {
        return 1221598221;
    }

    /**
     * returns the sources
     *
     * @return  array
     */
    public function getSources(): array
    {
        return [['name' => 'extendedSourceName', 'url' => 'extendedSourceUrl']];
    }

    /**
     * returns the content
     *
     * @return  string
     */
    public function getContent(): string
    {
        return 'extendedContent';
    }
}