<?php
declare(strict_types=1);
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  stubbles\xml
 */
namespace stubbles\xml\rss;
use stubbles\xml\XmlStreamWriter;
use stubbles\xml\serializer\ObjectXmlSerializer;
use stubbles\xml\serializer\XmlSerializer;
/**
 * Class for a rss 2.0 feed item.
 *
 * @see  http://rssboard.org/rss-specification
 */
class RssFeedItemSerializer implements ObjectXmlSerializer
{
    /**
     * serializes given value
     *
     * @param   mixed                                   $object
     * @param   \stubbles\xml\serializer\XmlSerializer  $xmlSerializer  serializer in case $value is not just a scalar value
     * @param   \stubbles\xml\XmlStreamWriter           $xmlWriter      xml writer to write serialized object into
     * @param   string                                  $tagName        name of the surrounding xml tag
     * @throws  \InvalidArgumentException  in case $object is not an instance of stubbles\xml\rss\RssFeedItem
     */
    public function serialize($object, XmlSerializer $xmlSerializer, XmlStreamWriter $xmlWriter, string $tagName = null)
    {
        if (!($object instanceof RssFeedItem)) {
            throw new \InvalidArgumentException('Oject must be of type stubbles\xml\rss\RssFeedItem');
        }

        $xmlWriter->writeStartElement(null !== $tagName ? $tagName : 'item');
        $xmlWriter->writeElement('title', [], $object->getTitle());
        $xmlWriter->writeElement('link', [], $object->getLink());
        $xmlWriter->writeElement('description', [], $object->getDescription());
        if ($object->hasAuthor()) {
            $xmlWriter->writeElement('author', [], $object->getAuthor());
        }

        foreach ($object->getCategories() as $category) {
            $attributes = [];
            if (isset($category['domain']) && strlen($category['domain']) > 0) {
                $attributes['domain'] = $category['domain'];
            }

            $xmlWriter->writeElement('category', $attributes, $category['category']);
        }

        if ($object->hasComments()) {
            $xmlWriter->writeElement('comments', [], $object->getComments());
        }

        foreach ($object->getEnclosures() as $enclosure) {
            $xmlWriter->writeElement('enclosure', ['url'    => $enclosure['url'],
                                                   'length' => $enclosure['length'],
                                                   'type'   => $enclosure['type']
                                                  ]
            );
        }

        if ($object->hasGuid()) {
            $xmlWriter->writeElement('guid',
                                     ['isPermaLink' => $xmlSerializer->convertBoolToString($object->isGuidPermaLink())],
                                     $object->getGuid()
            );
        }

        if ($object->hasPubDate()) {
            $xmlWriter->writeElement('pubDate', [], $object->getPubDate());
        }

        foreach ($object->getSources() as $source) {
            $xmlWriter->writeElement('source', ['url' => $source['url']], $source['name']);
        }

        if ($object->hasContent()) {
            $xmlWriter->writeElement('content:encoded', [], $object->getContent());
        }

        $xmlWriter->writeEndElement();
    }
}
