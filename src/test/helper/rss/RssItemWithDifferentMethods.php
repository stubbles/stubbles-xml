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
    public function getHeadline(): string
    {
        return 'headline';
    }

    /**
     * returns the link
     *
     * @return  string
     */
    public function getUrl(): string
    {
        return 'url';
    }

    /**
     * returns the description
     *
     * @return  string
     */
    public function getTeaser(): string
    {
        return 'teaser';
    }

    /**
     * returns the author
     *
     * @return  string
     */
    public function getCreator(): string
    {
        return 'creator@example.com (creator)';
    }

    /**
     * returns the categories
     *
     * @return  array
     */
    public function getTags(): array
    {
        return [['category' => 'tag1', 'domain'   => 'other']];
    }

    /**
     * returns the comments url
     *
     * @return  string
     */
    public function getRemarks(): string
    {
        return 'remarks';
    }

    /**
     * returns the enclosures
     *
     * @return  array
     */
    public function getImages(): array
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
    public function getId(): string
    {
        return 'id';
    }

    /**
     * returns whether guid is perma link or not
     *
     * @return  bool
     */
    public function isPermanent(): bool
    {
        return false;
    }

    /**
     * returns the publishing date
     *
     * @return  int
     */
    public function getDate(): int
    {
        return 1221598221;
    }

    /**
     * returns the sources
     *
     * @return  array
     */
    public function getOrigin(): array
    {
        return [['name' => 'originName', 'url' => 'originUrl']];
    }

    /**
     * returns the content
     *
     * @return  string
     */
    public function getText(): string
    {
        return 'text';
    }
}