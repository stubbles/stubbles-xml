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
use stubbles\xml\rss\RssFeedItem as RssRssFeedItem;
use stubbles\xml\XmlException;

#[Attribute(Attribute::TARGET_CLASS)]
class RssFeedItem
{
    private const array METHODS = [
        'byAuthor'              => 'authorMethod',
        'inCategories'          => 'categoriesMethod',
        'addCommentsAt'         => 'getCommentsUrlMethod',
        'deliveringEnclosures'  => 'enclosuresMethod',
        'withGuid'              => 'guidMethod',
        'andGuidIsNotPermaLink' => 'isPermaLinkMethod',
        'publishedOn'           => 'pubDateMethod',
        'inspiredBySources'     => 'sourcesMethod',
        'withContent'           => 'contentMethod'
    ];

    public function __construct(
        private string $titleMethod = 'getTitle',
        private string $linkMethod = 'getLink',
        private string $descriptionMethod = 'getDescription',
        private string $authorMethod = 'getAuthor',
        private string $categoriesMethod = 'getCategories',
        private string $getCommentsUrlMethod = 'getCommentsUrl',
        private string $enclosuresMethod = 'getEnclosures',
        private string $guidMethod = 'getGuid',
        private string $isPermaLinkMethod = 'isPermaLink',
        private string $pubDateMethod = 'getPubDate',
        private string $sourcesMethod = 'getSources',
        private string $contentMethod = 'getContent'
    ) {}

    public function extract(object $entity, array $overrides = []): RssRssFeedItem
    {
        $rssFeedItem = new RssRssFeedItem(
            $overrides['title'] ?? $this->getRequiredAttribute($entity, 'title', $this->titleMethod),
            $overrides['link'] ?? $this->getRequiredAttribute($entity, 'link', $this->linkMethod),
            $overrides['description'] ?? $this->getRequiredAttribute($entity, 'description', $this->descriptionMethod),
        );
        foreach (self::METHODS as $itemMethod => $property) {
            if (isset($overrides[$itemMethod])) {
                $rssFeedItem->$itemMethod($overrides[$itemMethod]);
                continue;
            }

            $entityMethod = $this->$property;
            if (method_exists($entity, $entityMethod)) {
                $rssFeedItem->$itemMethod($entity->$entityMethod());
            }
        }

        return $rssFeedItem;
    }

    private function getRequiredAttribute(
        object $entity,
        string $name,
        string $method
    ): string {
        if (!method_exists($entity, $method)) {
            throw new XmlException(
                sprintf(
                    'RSSFeedItem of type %s does not offer a method named "%s"'
                    . ' to return the %s, but %s is required.',
                    get_class($entity),
                    $method,
                    $name,
                    $name
                )
            );
        }

        return $entity->$method();
    }
}