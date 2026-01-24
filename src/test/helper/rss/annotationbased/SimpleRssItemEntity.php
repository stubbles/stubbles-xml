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
class SimpleRssItemEntity extends MissingDescriptionRssItemEntity
{
    /**
     * returns the description
     */
    public function getDescription(): string
    {
        return 'simpleDescription';
    }
}