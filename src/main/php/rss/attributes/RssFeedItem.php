<?php
declare(strict_types=1);
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace stubbles\xml\rss\attributes;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS)]
class RssFeedItem
{
    public function __construct(
        public readonly string $titleMethod = 'getTitle',
        public readonly string $linkMethod = 'getLink',
        public readonly string $descriptionMethod = 'getDescription',
        public readonly string $authorMethod = 'getAuthor',
        public readonly string $categoriesMethod = 'getCategories',
        public readonly string $getCommentsUrlMethod = 'getCommentsUrl',
        public readonly string $enclosuresMethod = 'getEnclosures',
        public readonly string $guidMethod = 'getGuid',
        public readonly string $isPermaLinkMethod = 'isPermaLink',
        public readonly string $pubDateMethod = 'getPubDate',
        public readonly string $sourcesMethod = 'getSources',
        public readonly string $contentMethod = 'getContent'
    ) {}
}