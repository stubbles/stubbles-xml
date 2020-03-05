<?php
declare(strict_types=1);
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace stubbles\xml\rss;
use stubbles\xml\XmlStreamWriter;
use stubbles\xml\serializer\ObjectXmlSerializer;
use stubbles\xml\serializer\XmlSerializer;
/**
 * Class for a rss 2.0 feed item.
 *
 * @see  http://rssboard.org/rss-specification
 * @implements ObjectXmlSerializer<RssFeedItem>
 */
class RssFeedItemSerializer implements ObjectXmlSerializer
{
    /**
     * serializes given value
     *
     * @param   RssFeedItem                             $rssFeedItem
     * @param   \stubbles\xml\serializer\XmlSerializer  $xmlSerializer  serializer in case $value is not just a scalar value
     * @param   \stubbles\xml\XmlStreamWriter           $xmlWriter      xml writer to write serialized object into
     * @param   string                                  $tagName        name of the surrounding xml tag
     * @throws  \InvalidArgumentException  in case $rssFeedItem is not an instance of stubbles\xml\rss\RssFeedItem
     */
    public function serialize(
            object $rssFeedItem,
            XmlSerializer $xmlSerializer,
            XmlStreamWriter $xmlWriter,
            string $tagName = null
    ): void {
        if (!($rssFeedItem instanceof RssFeedItem)) {
            throw new \InvalidArgumentException('Oject must be of type stubbles\xml\rss\RssFeedItem');
        }

        $xmlWriter->writeStartElement(null !== $tagName ? $tagName : 'item')
                ->writeElement('title', [], $rssFeedItem->title())
                ->writeElement('link', [], $rssFeedItem->link())
                ->writeElement('description', [], $rssFeedItem->description());
        if ($rssFeedItem->hasAuthor()) {
            $xmlWriter->writeElement('author', [], $rssFeedItem->author());
        }

        foreach ($rssFeedItem->categories() as $category) {
            $attributes = [];
            if (isset($category['domain']) && strlen($category['domain']) > 0) {
                $attributes['domain'] = $category['domain'];
            }

            $xmlWriter->writeElement('category', $attributes, $category['category']);
        }

        if ($rssFeedItem->hasComments()) {
            $xmlWriter->writeElement('comments', [], $rssFeedItem->comments());
        }

        foreach ($rssFeedItem->enclosures() as $enclosure) {
            $xmlWriter->writeElement('enclosure', $enclosure);
        }

        if ($rssFeedItem->hasGuid()) {
            $xmlWriter->writeElement(
                    'guid',
                    ['isPermaLink' => $xmlSerializer->convertBoolToString($rssFeedItem->isGuidPermaLink())],
                    $rssFeedItem->guid()
            );
        }

        if ($rssFeedItem->hasPubDate()) {
            $xmlWriter->writeElement('pubDate', [], $rssFeedItem->pubDate());
        }

        foreach ($rssFeedItem->sources() as $source) {
            $xmlWriter->writeElement('source', ['url' => $source['url']], $source['name']);
        }

        if ($rssFeedItem->hasContent()) {
            $xmlWriter->writeElement('content:encoded', [], $rssFeedItem->content());
        }

        $xmlWriter->writeEndElement();
    }
}
