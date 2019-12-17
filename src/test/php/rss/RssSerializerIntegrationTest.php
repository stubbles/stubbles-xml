<?php
declare(strict_types=1);
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace stubbles\xml\rss;
use PHPUnit\Framework\TestCase;
use stubbles\date\Date;
use stubbles\date\TimeZone;
use stubbles\ioc\Binder;
use stubbles\xml\serializer\XmlSerializerFacade;

use function bovigo\assert\assertThat;
use function bovigo\assert\predicate\equals;
/**
 * Test for stubbles\xml\rss\RssFeedSerializer.
 *
 * @group  xml
 * @group  xml_rss
 */
class RssSerializerIntegrationTest extends TestCase
{
    /**
     * @test
     */
    public function writeFeed(): void
    {
        $binder     = new Binder();
        /** @var  XmlSerializerFacade  $serializer */
        $serializer = $binder->getInjector()->getInstance(XmlSerializerFacade::class);
        $dom = $serializer->serializeToDom($this->createFeed());
        $dom->formatOutput = true;
        assertThat(
                $dom->saveXML(),
                equals('<?xml version="1.0" encoding="UTF-8"?>
<rss version="2.0" xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#" xmlns:content="http://purl.org/rss/1.0/modules/content/">
  <channel>
    <title>Example rss feed</title>
    <link>http://example.net/rss/</link>
    <description>An example RSS feed</description>
    <generator>Stubbles RssFeedGenerator</generator>
    <language>en_EN</language>
    <copyright>(c) 2012 example.net</copyright>
    <managingEditor>nospam@example.com (Master Yoda)</managingEditor>
    <webMaster>nospam@example.com (Mr Hermann)</webMaster>
    <lastBuildDate>Sun 22 Jan 2012 00:00:00 +0100</lastBuildDate>
    <ttl>86400</ttl>
    <image>
      <url>http://example.net/logo.png</url>
      <title>Example rss feed</title>
      <link>http://example.net/rss/</link>
      <width>100</width>
      <height>80</height>
      <description>An example rss feed</description>
    </image>
    <item>
      <title>Entry 1</title>
      <link>http://example.net/article/1</link>
      <description>A first article</description>
      <author>nospam@example.com (mikey)</author>
      <category>live</category>
      <category>examples</category>
      <comments>http://example.net/article/1/comments</comments>
      <guid isPermaLink="true">http://example.net/article/1</guid>
      <pubDate>Sat 21 Jan 2012 00:00:00 +0100</pubDate>
      <content:encoded>Some article content</content:encoded>
    </item>
  </channel>
</rss>
')
        );
    }

    private function createFeed(): RssFeed
    {
        $rssFeed = new RssFeed('Example rss feed', 'http://example.net/rss/', 'An example RSS feed');
        $rssFeed->setCopyright('(c) 2012 example.net')
                ->setLocale('en_EN')
                ->setLastBuildDate(new Date('2012-01-22', new TimeZone('Europe/Berlin')))
                ->setManagingEditor('Master Yoda')
                ->setWebMaster('Mr Hermann')
                ->setTimeToLive(86400)
                ->setImage('http://example.net/logo.png', 'An example rss feed', 100, 80);
        $rssFeed->addItem('Entry 1', 'http://example.net/article/1', 'A first article')
                ->byAuthor('mikey')
                ->addCommentsAt('http://example.net/article/1/comments')
                ->inCategories(['live', 'examples'])
                ->publishedOn(new Date('2012-01-21', new TimeZone('Europe/Berlin')))
                ->withContent('Some article content')
                ->withGuid('http://example.net/article/1');
        return $rssFeed;
    }
}
