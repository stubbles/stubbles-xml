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
class SimpleRssItemEntity extends MissingDescriptionRssItemEntity
{
    /**
     * returns the description
     *
     * @return  string
     */
    public function getDescription(): string
    {
        return 'simpleDescription';
    }
}