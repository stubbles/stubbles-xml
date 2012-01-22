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
class RssFeedSerializer extends BaseObject implements ObjectXmlSerializer
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
            throw new IllegalArgumentException('Oject must be of type net\\stubbles\\xml\\rss\\RssFeed');
        }

        foreach ($object->getStylesheets() as $stylesheet) {
            $xmlWriter->writeProcessingInstruction('xml-stylesheet', 'href="' . $stylesheet . '" type="text/xsl"');
        }

        $xmlWriter->writeStartElement(((null == $tagName) ? ('rss') : ($tagName)));
        $xmlWriter->writeAttribute('version', '2.0');
        $xmlWriter->writeAttribute('xmlns:rdf', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#');
        $xmlWriter->writeAttribute('xmlns:content', 'http://purl.org/rss/1.0/modules/content/');

        $xmlWriter->writeStartElement('channel');
        $xmlWriter->writeElement('title', array(), $object->getTitle());
        $xmlWriter->writeElement('link', array(), $object->getLink());
        $xmlWriter->writeElement('description', array(), $object->getDescription());
        $xmlWriter->writeElement('generator', array(), $this->generator);

        if ($object->hasLocale()) {
            $xmlWriter->writeElement('language', array(), $object->getLocale());
        }

        if ($object->hasCopyright()) {
            $xmlWriter->writeElement('copyright', array(), $object->getCopyright());
        }

        if ($object->hasManagingEditor()) {
            $xmlWriter->writeElement('managingEditor', array(), $object->getManagingEditor());
        }

        if ($object->hasWebMaster()) {
            $xmlWriter->writeElement('webMaster', array(), $object->getWebMaster());
        }

        if ($object->hasLastBuildDate()) {
            $xmlWriter->writeElement('lastBuildDate', array(), $object->getLastBuildDate());
        }

        if ($object->hasTimeToLive()) {
            $xmlWriter->writeElement('ttl', array(), $object->getTimeToLive());
        }

        if ($object->hasImage()) {
            $xmlWriter->writeStartElement('image');
            $xmlWriter->writeElement('url', array(), $object->getImageUrl());
            $xmlWriter->writeElement('title', array(), $object->getTitle());
            $xmlWriter->writeElement('link', array(), $object->getLink());
            $xmlWriter->writeElement('width', array(), $object->getImageWidth());
            $xmlWriter->writeElement('height', array(), $object->getImageHeight());
            $xmlWriter->writeElement('description', array(), $object->getImageDescription());
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
?>