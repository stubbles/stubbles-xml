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
     * @var  string
     */
    private $generator = 'Stubbles RssFeedGenerator';

    /**
     * set the generator of the feed
     *
     * @param   string  $generator  name of the generator to use
     * @return  \stubbles\xml\rss\RssFeedSerializer
     */
    public function setGenerator(string $generator): self
    {
        $this->generator = $generator;
        return $this;
    }

    /**
     * serializes given value
     *
     * @param   mixed                                   $rssFeed
     * @param   \stubbles\xml\serializer\XmlSerializer  $xmlSerializer  serializer in case $value is not just a scalar value
     * @param   \stubbles\xml\XmlStreamWriter           $xmlWriter      xml writer to write serialized object into
     * @param   string                                  $tagName        name of the surrounding xml tag
     * @throws  \InvalidArgumentException  in case $rssFeed is not an instance of stubbles\xml\rss\RssFeed
     */
    public function serialize(
            $rssFeed,
            XmlSerializer $xmlSerializer,
            XmlStreamWriter $xmlWriter,
            string $tagName = null
    ): void {
        if (!($rssFeed instanceof RssFeed)) {
            throw new \InvalidArgumentException('Oject must be of type stubbles\xml\rss\RssFeed');
        }

        foreach ($rssFeed->stylesheets() as $stylesheet) {
            $xmlWriter->writeProcessingInstruction(
                    'xml-stylesheet',
                    'href="' . $stylesheet . '" type="text/xsl"'
            );
        }

        $xmlWriter->writeStartElement(null !== $tagName ? $tagName : 'rss')
                ->writeAttribute('version', '2.0')
                ->writeAttribute('xmlns:rdf', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#')
                ->writeAttribute('xmlns:content', 'http://purl.org/rss/1.0/modules/content/');

        $xmlWriter->writeStartElement('channel')
                ->writeElement('title', [], $rssFeed->title())
                ->writeElement('link', [], $rssFeed->link())
                ->writeElement('description', [], $rssFeed->description())
                ->writeElement('generator', [], $this->generator);

        if ($rssFeed->hasLocale()) {
            $xmlWriter->writeElement('language', [], $rssFeed->locale());
        }

        if ($rssFeed->hasCopyright()) {
            $xmlWriter->writeElement('copyright', [], $rssFeed->copyright());
        }

        if ($rssFeed->hasManagingEditor()) {
            $xmlWriter->writeElement('managingEditor', [], $rssFeed->managingEditor());
        }

        if ($rssFeed->hasWebMaster()) {
            $xmlWriter->writeElement('webMaster', [], $rssFeed->webMaster());
        }

        if ($rssFeed->hasLastBuildDate()) {
            $xmlWriter->writeElement('lastBuildDate', [], $rssFeed->lastBuildDate());
        }

        if ($rssFeed->hasTimeToLive()) {
            $xmlWriter->writeElement('ttl', [], (string) $rssFeed->timeToLive());
        }

        if ($rssFeed->hasImage()) {
            $xmlWriter->writeStartElement('image')
                    ->writeElement('url', [], $rssFeed->imageUrl())
                    ->writeElement('title', [], $rssFeed->title())
                    ->writeElement('link', [], $rssFeed->link())
                    ->writeElement('width', [], (string) $rssFeed->imageWidth())
                    ->writeElement('height', [], (string) $rssFeed->imageHeight())
                    ->writeElement('description', [], $rssFeed->imageDescription())
                    ->writeEndElement();
        }

        foreach ($rssFeed->items() as $item) {
            $xmlSerializer->serializeObject($item, $xmlWriter);
        }

        $xmlWriter->writeEndElement() // </channel>
                  ->writeEndElement(); // </rss>
    }
}
