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
class MissingDescriptionRssItemEntity extends MissingLinkAndDescriptionRssItemEntity
{
    /**
     * returns the link
     */
    public function getLink(): string
    {
        return 'simpleLink';
    }
}