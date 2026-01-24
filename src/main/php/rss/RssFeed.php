<?php
declare(strict_types=1);
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace stubbles\xml\rss;

use InvalidArgumentException;
use stubbles\date\Date;
use stubbles\xml\rss\attributes\RssFeedItem as RssFeedItemAttribute;
use stubbles\xml\serializer\attributes\XmlSerializer;

use function stubbles\reflect\attributesOf;

/**
 * Class for representing a rss 2.0 feed.
 */
#[XmlSerializer(RssFeedSerializer::class)]
class RssFeed
{
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

    public function __construct(
        private string $title,
        private string $link,
        private string $description)
    { }

    /**
     * returns the title of rss feed
     */
    public function title(): string
    {
        return $this->title;
    }

    /**
     * returns the source of rss feed
     */
    public function link(): string
    {
        return $this->link;
    }

    /**
     * returns the source description
     */
    public function description(): string
    {
        return $this->description;
    }

    /**
     * set the locale the channel is written in
     */
    public function setLocale(string $locale): self
    {
        $this->locale = $locale;
        return $this;
    }

    /**
     * checks if locale is set
     */
    public function hasLocale(): bool
    {
        return null !== $this->locale;
    }

    /**
     * returns the locale
     */
    public function locale(): ?string
    {
        return $this->locale;
    }

    /**
     * set copyright notice for content in the channel
     */
    public function setCopyright(string $copyright): self
    {
        $this->copyright = $copyright;
        return $this;
    }

    /**
     * checks if copyright is set
     */
    public function hasCopyright(): bool
    {
        return null !== $this->copyright;
    }

    /**
     * returns the copyright notice
     */
    public function copyright(): ?string
    {
        return $this->copyright;
    }

    /**
     * add an item to the feed and returns it
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
     * @param   array<string,mixed>  $overrides
     */
    public function addEntity(object $entity, array $overrides = []): RssFeedItem
    {
        $attributes = attributesOf($entity);
        if ($attributes->contain(RssFeedItemAttribute::class)) {
            return $this->items[] = $attributes->firstNamed(RssFeedItemAttribute::class)->extract($entity, $overrides);
        }

        return $this->items[] = RssFeedItem::fromEntity($entity, $overrides);
    }

    /**
     * checks whether an item is present at given position
     */
    public function hasItem(int $pos): bool
    {
        return isset($this->items[$pos]);
    }

    /**
     * returns item at given position
     */
    public function item(int $pos): ?RssFeedItem
    {
        return $this->items[$pos] ?? null;
    }

    /**
     * returns a list of all items
     *
     * @return  RssFeedItem[]
     */
    public function items(): array
    {
        return $this->items;
    }

    /**
     * returns the number of items added for this feed
     */
    public function countItems(): int
    {
        return count($this->items);
    }

    /**
     * append a stylesheet to the document
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
     */
    public function hasManagingEditor(): bool
    {
        return null !== $this->managingEditor;
    }

    /**
     * returns the email address for person responsible for editorial content
     */
    public function managingEditor(): ?string
    {
        return $this->managingEditor;
    }

    /**
     * set email address for person responsible for technical issues relating to channel
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
     */
    public function hasWebMaster(): bool
    {
        return null !== $this->webMaster;
    }

    /**
     * returns the email address for person responsible for technical issues relating to channel
     */
    public function webMaster(): ?string
    {
        return $this->webMaster;
    }

    /**
     * set the last time when the content of the channel changed
     */
    public function setLastBuildDate(int|string|Date $lastBuildDate): self
    {
        $this->lastBuildDate = Date::castFrom($lastBuildDate, 'lastBuildDate');
        return $this;
    }

    /**
     * checks if last build date is set
     */
    public function hasLastBuildDate(): bool
    {
        return null !== $this->lastBuildDate;
    }

    /**
     * returns the last build date
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
     */
    public function setTimeToLive(int $ttl): self
    {
        $this->ttl = $ttl;
        return $this;
    }

    /**
     * checks if time to live is set
     */
    public function hasTimeToLive(): bool
    {
        return null !== $this->ttl;
    }

    /**
     * number of minutes that indicates how long a channel can be cached
     * before refreshing from the source
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
     * @throws  InvalidArgumentException  in case $width or $height have invalid values
     */
    public function setImage(
            string $url,
            string $description,
            int $width = 88,
            int $height = 31
    ): self {
        if (144 < $width || 0 > $width) {
            throw new InvalidArgumentException('Width must be a value between 0 and 144.');
        }

        if (400 < $height || 0 > $height) {
            throw new InvalidArgumentException('Height must be a value between 0 and 400.');
        }

        $this->imageUrl         = $url;
        $this->imageDescription = $description;
        $this->imageWidth       = $width;
        $this->imageHeight      = $height;
        return $this;
    }

    /**
     * checks if image is available
     */
    public function hasImage(): bool
    {
        return strlen($this->imageUrl) > 0;
    }

    /**
     * returns url of feed image
     */
    public function imageUrl(): string
    {
        return $this->imageUrl;
    }

    /**
     * returns description of feed image
     */
    public function imageDescription(): string
    {
        return $this->imageDescription;
    }

    /**
     * returns width of feed image in pixels
     */
    public function imageWidth(): int
    {
        return $this->imageWidth;
    }

    /**
     * returns height of feed image in pixels
     */
    public function imageHeight(): int
    {
        return $this->imageHeight;
    }
}
