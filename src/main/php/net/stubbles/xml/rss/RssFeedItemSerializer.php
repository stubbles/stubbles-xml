<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  net\stubbles\xml
 */
namespace net\stubbles\xml\rss;
use net\stubbles\lang\BaseObject;
use net\stubbles\lang\exception\IllegalArgumentException;
use net\stubbles\xml\serializer\ObjectXmlSerializer;
use net\stubbles\xml\serializer\XmlSerializer;
use net\stubbles\xml\XmlStreamWriter;
/**
 * Class for a rss 2.0 feed item.
 *
 * @see  http://rssboard.org/rss-specification
 */
class RssFeedItemSerializer extends BaseObject implements ObjectXmlSerializer
{
    /**
     * serializes given value
     *
     * @param  mixed            $object
     * @param  XmlSerializer    $xmlSerializer  serializer in case $value is not just a scalar value
     * @param  XmlStreamWriter  $xmlWriter      xml writer to write serialized object into
     * @param  string           $tagName        name of the surrounding xml tag
     */
    public function serialize($object, XmlSerializer $xmlSerializer, XmlStreamWriter $xmlWriter, $tagName)
    {
        if (!($object instanceof RssFeedItem)) {
            throw new IllegalArgumentException('Oject must be of type net\\stubbles\\xml\\rss\\RssFeedItem');
        }

        $xmlWriter->writeStartElement(((null == $tagName) ? ('item') : ($tagName)));
        $xmlWriter->writeElement('title', array(), $object->getTitle());
        $xmlWriter->writeElement('link', array(), $object->getLink());
        $xmlWriter->writeElement('description', array(), $object->getDescription());
        if ($object->hasAuthor()) {
            $xmlWriter->writeElement('author', array(), $object->getAuthor());
        }

        foreach ($object->getCategories() as $category) {
            $attributes = array();
            if (isset($category['domain']) && strlen($category['domain']) > 0) {
                $attributes['domain'] = $category['domain'];
            }

            $xmlWriter->writeElement('category', $attributes, $category['category']);
        }

        if ($object->hasComments()) {
            $xmlWriter->writeElement('comments', array(), $object->getComments());
        }

        foreach ($object->getEnclosures() as $enclosure) {
            $xmlWriter->writeElement('enclosure', array('url'    => $enclosure['url'],
                                                        'length' => $enclosure['length'],
                                                        'type'   => $enclosure['type']
                                                  )
            );
        }

        if ($object->hasGuid()) {
            $xmlWriter->writeElement('guid',
                                     array('isPermaLink' => $xmlSerializer->convertBoolToString($object->isGuidPermaLink())),
                                     $object->getGuid()
            );
        }

        if ($object->hasPubDate()) {
            $xmlWriter->writeElement('pubDate', array(), $object->getPubDate());
        }

        foreach ($object->getSources() as $source) {
            $xmlWriter->writeElement('source', array('url' => $source['url']), $source['name']);
        }

        if ($object->hasContent()) {
            $xmlWriter->writeElement('content:encoded', array(), $object->getContent());
        }

        $xmlWriter->writeEndElement();
    }
}
?>