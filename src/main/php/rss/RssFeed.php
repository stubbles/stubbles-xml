<?php
declare(strict_types=1);
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace stubbles\xml\rss;
use stubbles\date\Date;
/**
 * Class for representing a rss 2.0 feed.
 *
 * @XmlSerializer(stubbles\xml\rss\RssFeedSerializer.class)
 */
class RssFeed
{
    /**
     * name of the channel
     *
     * @var  string
     */
    private $title;
    /**
     * URL to the HTML website corresponding to the channel
     *
     * @var  string
     */
    private $link;
    /**
     * phrase or sentence describing the channel
     *
     * @var  string
     */
    private $description;
    /**
     * list of items in feed
     *
     * @var  RssFeedItem[]
     */
    private $items          = [];
    /**
     * list of stylesheets to append as processing instructions
     *
     * @var  string[]
     */
    private $stylesheets    = [];
    /**
     * the locale the channel is written in
     *
     * @var  string|null
     * @see  http://rssboard.org/rss-language-codes
     */
    private $locale         = null;
    /**
     * copyright notice for content in the channel
     *
     * @var  string|null
     */
    private $copyright      = null;
    /**
     * email address for person responsible for editorial content
     *
     * @var  string|null
     */
    private $managingEditor = null;
    /**
     * email address for person responsible for technical issues relating to channel
     *
     * @var  string|null
     */
    private $webMaster      = null;
    /**
     * last time the content of the channel changed
     *
     * @var  \stubbles\date\Date|null
     */
    private $lastBuildDate  = null;
    /**
     * number of minutes that indicates how long a channel can be cached before refreshing from the source
     *
     * @var  int|null
     */
    private $ttl            = null;
    /**
     * specifies a GIF, JPEG or PNG image that can be displayed with the channel
     * @var  string
     */
    private $imageUrl = '';
    /** @var  string */
    private $imageDescription = '';
    /** @var  int */
    private $imageWidth = 88;
    /** @var  int */
    private $imageHeight = 31;

    /**
     * constructor
     *
     * @param  string  $title        title of rss feed
     * @param  string  $link         source of rss feed
     * @param  string  $description  source description
     */
    public function __construct(string $title, string $link, string $description)
    {
        $this->title       = $title;
        $this->link        = $link;
        $this->description = $description;
    }

    /**
     * returns the title of rss feed
     *
     * @return  string
     */
    public function title(): string
    {
        return $this->title;
    }

    /**
     * returns the source of rss feed
     *
     * @return  string
     */
    public function link(): string
    {
        return $this->link;
    }

    /**
     * returns the source description
     *
     * @return  string
     */
    public function description(): string
    {
        return $this->description;
    }

    /**
     * set the locale the channel is written in
     *
     * @param   string  $locale
     * @return  \stubbles\xml\rss\RssFeed
     */
    public function setLocale(string $locale): self
    {
        $this->locale = $locale;
        return $this;
    }

    /**
     * checks if locale is set
     *
     * @return  bool
     */
    public function hasLocale(): bool
    {
        return (null !== $this->locale);
    }

    /**
     * returns the locale
     *
     * @return  string
     */
    public function locale(): ?string
    {
        return $this->locale;
    }

    /**
     * set copyright notice for content in the channel
     *
     * @param   string  $copyright
     * @return  \stubbles\xml\rss\RssFeed
     */
    public function setCopyright(string $copyright): self
    {
        $this->copyright = $copyright;
        return $this;
    }

    /**
     * checks if copyright is set
     *
     * @return  bool
     */
    public function hasCopyright(): bool
    {
        return (null !== $this->copyright);
    }

    /**
     * returns the copyright notice
     *
     * @return  string
     */
    public function copyright(): ?string
    {
        return $this->copyright;
    }

    /**
     * add an item to the feed and returns it
     *
     * @param   string  $title        title of the item
     * @param   string  $link         URL of the item
     * @param   string  $description  item synopsis
     * @return  \stubbles\xml\rss\RssFeedItem
     */
    public function addItem(string $title, string $link, string $description): RssFeedItem
    {
        return $this->items[] = RssFeedItem::create($title, $link, $description);
    }

    /**
     * creates an item from an entity to the rss feed
     *
     * Return value is the created item.
     *
     * @param   object               $entity
     * @param   array<string,mixed>  $overrides
     * @return  \stubbles\xml\rss\RssFeedItem
     */
    public function addEntity($entity, array $overrides = []): RssFeedItem
    {
        return $this->items[] = RssFeedItem::fromEntity($entity, $overrides);
    }

    /**
     * checks whether an item is present at given position
     *
     * @param   int   $pos
     * @return  bool
     */
    public function hasItem(int $pos): bool
    {
        return isset($this->items[$pos]);
    }

    /**
     * returns item at given position
     *
     * @param   int  $pos
     * @return  \stubbles\xml\rss\RssFeedItem
     */
    public function item(int $pos): ?RssFeedItem
    {
        return $this->items[$pos] ?? null;
    }

    /**
     * returns a list of all items
     *
     * @return  \stubbles\xml\rss\RssFeedItem[]
     */
    public function items(): array
    {
        return $this->items;
    }

    /**
     * returns the number of items added for this feed
     *
     * @return  int
     */
    public function countItems(): int
    {
        return count($this->items);
    }

    /**
     * append a stylesheet to the document
     *
     * @param   string  $stylesheet  the stylesheet to append
     * @return  \stubbles\xml\rss\RssFeed
     */
    public function appendStylesheet(string $stylesheet): self
    {
        $this->stylesheets[] = $stylesheet;
        return $this;
    }

    /**
     * returns list of stylesheets
     *
     * @return  string[]
     */
    public function stylesheets(): array
    {
        return $this->stylesheets;
    }

    /**
     * set email address for person responsible for editorial content
     *
     * @param   string  $managingEditor
     * @return  \stubbles\xml\rss\RssFeed
     */
    public function setManagingEditor(string $managingEditor): self
    {
        if (!strstr($managingEditor, '@')) {
            $this->managingEditor = 'nospam@example.com (' . $managingEditor . ')';
        } else {
            $this->managingEditor = $managingEditor;
        }

        return $this;
    }

    /**
     * checks if managing editor is set
     *
     * @return  bool
     */
    public function hasManagingEditor(): bool
    {
        return (null !== $this->managingEditor);
    }

    /**
     * returns the email address for person responsible for editorial content
     *
     * @return  string
     */
    public function managingEditor(): ?string
    {
        return $this->managingEditor;
    }

    /**
     * set email address for person responsible for technical issues relating to channel
     *
     * @param   string  $webMaster
     * @return  \stubbles\xml\rss\RssFeed
     */
    public function setWebMaster(string $webMaster): self
    {
        if (!strstr($webMaster, '@')) {
            $this->webMaster = 'nospam@example.com (' . $webMaster . ')';
        } else {
            $this->webMaster = $webMaster;
        }

        return $this;
    }

    /**
     * checks if webMaster is set
     *
     * @return  bool
     */
    public function hasWebMaster(): bool
    {
        return (null !== $this->webMaster);
    }

    /**
     * returns the email address for person responsible for technical issues relating to channel
     *
     * @return  string
     */
    public function webMaster(): ?string
    {
        return $this->webMaster;
    }

    /**
     * set the last time when the content of the channel changed
     *
     * @param   string|int|\stubbles\date\Date   $lastBuildDate  last time the content of the channel changed
     * @return  \stubbles\xml\rss\RssFeed
     */
    public function setLastBuildDate($lastBuildDate): self
    {
        $this->lastBuildDate = Date::castFrom($lastBuildDate, 'lastBuildDate');
        return $this;
    }

    /**
     * checks if last build date is set
     *
     * @return  bool
     */
    public function hasLastBuildDate(): bool
    {
        return (null !== $this->lastBuildDate);
    }

    /**
     * returns the last build date
     *
     * @return  string
     */
    public function lastBuildDate(): ?string
    {
        if (null !== $this->lastBuildDate) {
            return $this->lastBuildDate->format('D d M Y H:i:s O');
        }

        return null;
    }

    /**
     * set number of minutes that indicates how long a channel can be cached
     * before refreshing from the source
     *
     * @param   int  $ttl
     * @return  \stubbles\xml\rss\RssFeed
     */
    public function setTimeToLive(int $ttl): self
    {
        $this->ttl = $ttl;
        return $this;
    }

    /**
     * checks if time to live is set
     *
     * @return  bool
     */
    public function hasTimeToLive(): bool
    {
        return (null !== $this->ttl);
    }

    /**
     * number of minutes that indicates how long a channel can be cached
     * before refreshing from the source
     *
     * @return  int
     */
    public function timeToLive(): ?int
    {
        return $this->ttl;
    }

    /**
     * specify a GIF, JPEG or PNG image to be displayed with the channel
     *
     * @param   string  $url          URL of a GIF, JPEG or PNG image that represents the channel
     * @param   string  $description  contains text that is included in the TITLE attribute of the link formed around the image in the HTML rendering
     * @param   int     $width        indicating the width of the image in pixels, must be 0 < $width <= 144, default 88
     * @param   int     $height       indicating the height of the image in pixels, must be 0 < $height <= 400, default 31
     * @return  \stubbles\xml\rss\RssFeed
     * @throws  \InvalidArgumentException  in case $width or $height have invalid values
     */
    public function setImage(
            string $url,
            string $description,
            int $width = 88,
            int $height = 31
    ): self {
        if (144 < $width || 0 > $width) {
            throw new \InvalidArgumentException('Width must be a value between 0 and 144.');
        }

        if (400 < $height || 0 > $height) {
            throw new \InvalidArgumentException('Height must be a value between 0 and 400.');
        }

        $this->imageUrl         = $url;
        $this->imageDescription = $description;
        $this->imageWidth       = $width;
        $this->imageHeight      = $height;
        return $this;
    }

    /**
     * checks if image is available
     *
     * @return  bool
     */
    public function hasImage(): bool
    {
        return (strlen($this->imageUrl) > 0);
    }

    /**
     * returns url of feed image
     *
     * @return  string
     */
    public function imageUrl(): string
    {
        return $this->imageUrl;
    }

    /**
     * returns description of feed image
     *
     * @return  string
     */
    public function imageDescription(): string
    {
        return $this->imageDescription;
    }

    /**
     * returns width of feed image in pixels
     *
     * @return  int
     */
    public function imageWidth(): int
    {
        return $this->imageWidth;
    }

    /**
     * returns height of feed image in pixels
     *
     * @return  int
     */
    public function imageHeight(): int
    {
        return $this->imageHeight;
    }
}
