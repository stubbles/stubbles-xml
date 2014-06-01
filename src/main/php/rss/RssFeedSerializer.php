<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  stubbles\xml
 */
namespace stubbles\xml\rss;
use stubbles\lang\exception\IllegalArgumentException;
use stubbles\xml\serializer\ObjectXmlSerializer;
use stubbles\xml\serializer\XmlSerializer;
use stubbles\xml\XmlStreamWriter;
/**
 * Class for generating a rss 2.0 feed.
 *
 * The implementation follows the rss specification available at
 * http://rssboard.org/rss-specification. However some of the elements are
 * not implemented:
 * pubDate
 * category   Why categorize a whole feed when the items can be categorized?
 * cloud      This implies security and spamming dangers.
 * rating
 * textInput  Most aggregators ignore it.
 * skipHours  Usage relies on behaviour of aggregators.
 * skipDays   Usage relies on behaviour of aggregators.
 *
 * @see  http://rssboard.org/rss-specification
 */
class RssFeedSerializer implements ObjectXmlSerializer
{
    /**
     * the generator of this rss feed
     *
     * @type  string
     */
    private $generator = 'Stubbles RssFeedGenerator';

    /**
     * set the generator of the feed
     *
     * @param   string  $generator  name of the generator to use
     * @return  RssFeedSerializer
     */
    public function setGenerator($generator)
    {
        $this->generator = $generator;
        return $this;
    }

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
        if (!($object instanceof RssFeed)) {
            throw new IllegalArgumentException('Oject must be of type stubbles\xml\rss\RssFeed');
        }

        foreach ($object->getStylesheets() as $stylesheet) {
            $xmlWriter->writeProcessingInstruction('xml-stylesheet', 'href="' . $stylesheet . '" type="text/xsl"');
        }

        $xmlWriter->writeStartElement(((null == $tagName) ? ('rss') : ($tagName)));
        $xmlWriter->writeAttribute('version', '2.0');
        $xmlWriter->writeAttribute('xmlns:rdf', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#');
        $xmlWriter->writeAttribute('xmlns:content', 'http://purl.org/rss/1.0/modules/content/');

        $xmlWriter->writeStartElement('channel');
        $xmlWriter->writeElement('title', [], $object->getTitle());
        $xmlWriter->writeElement('link', [], $object->getLink());
        $xmlWriter->writeElement('description', [], $object->getDescription());
        $xmlWriter->writeElement('generator', [], $this->generator);

        if ($object->hasLocale()) {
            $xmlWriter->writeElement('language', [], $object->getLocale());
        }

        if ($object->hasCopyright()) {
            $xmlWriter->writeElement('copyright', [], $object->getCopyright());
        }

        if ($object->hasManagingEditor()) {
            $xmlWriter->writeElement('managingEditor', [], $object->getManagingEditor());
        }

        if ($object->hasWebMaster()) {
            $xmlWriter->writeElement('webMaster', [], $object->getWebMaster());
        }

        if ($object->hasLastBuildDate()) {
            $xmlWriter->writeElement('lastBuildDate', [], $object->getLastBuildDate());
        }

        if ($object->hasTimeToLive()) {
            $xmlWriter->writeElement('ttl', [], $object->getTimeToLive());
        }

        if ($object->hasImage()) {
            $xmlWriter->writeStartElement('image');
            $xmlWriter->writeElement('url', [], $object->getImageUrl());
            $xmlWriter->writeElement('title', [], $object->getTitle());
            $xmlWriter->writeElement('link', [], $object->getLink());
            $xmlWriter->writeElement('width', [], $object->getImageWidth());
            $xmlWriter->writeElement('height', [], $object->getImageHeight());
            $xmlWriter->writeElement('description', [], $object->getImageDescription());
            $xmlWriter->writeEndElement();
        }

        foreach ($object->getItems() as $item) {
            $xmlSerializer->serializeObject($item, $xmlWriter);
        }

        $xmlWriter->writeEndElement();
        $xmlWriter->writeEndElement();
        return $xmlWriter;
    }
}
