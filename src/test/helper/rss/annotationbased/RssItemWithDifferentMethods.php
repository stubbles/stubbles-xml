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
 * @deprecated will be removed with 11.0.0
 */
class RssItemWithDifferentMethods
{
    /**
     * returns the title
     */
    public function getHeadline(): string
    {
        return 'headline';
    }

    /**
     * returns the link
     */
    public function getUrl(): string
    {
        return 'url';
    }

    /**
     * returns the description
     */
    public function getTeaser(): string
    {
        return 'teaser';
    }

    /**
     * returns the author
     */
    public function getCreator(): string
    {
        return 'creator@example.com (creator)';
    }

    /**
     * returns the categories
     */
    public function getTags(): array
    {
        return [['category' => 'tag1', 'domain'   => 'other']];
    }

    /**
     * returns the comments url
     */
    public function getRemarks(): string
    {
        return 'remarks';
    }

    /**
     * returns the enclosures
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
     */
    public function getId(): string
    {
        return 'id';
    }

    /**
     * returns whether guid is perma link or not
     */
    public function isPermanent(): bool
    {
        return false;
    }

    /**
     * returns the publishing date
     */
    public function getDate(): int
    {
        return 1221598221;
    }

    /**
     * returns the sources
     */
    public function getOrigin(): array
    {
        return [['name' => 'originName', 'url' => 'originUrl']];
    }

    /**
     * returns the content
     */
    public function getText(): string
    {
        return 'text';
    }
}