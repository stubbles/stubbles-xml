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
use stubbles\xml\XmlException;

use function stubbles\reflect\reflect;
use function stubbles\reflect\annotationsOf;
/**
 * Class for a rss 2.0 feed item.
 *
 * @see  http://rssboard.org/rss-specification
 * @XmlSerializer(stubbles\xml\rss\RssFeedItemSerializer.class)
 */
class RssFeedItem
{
    /**
     * map of methods to retrieve rss feed item data
     */
    const METHODS = [
            'byAuthor'              => 'getAuthor',
            'inCategories'          => 'getCategories',
            'addCommentsAt'         => 'getCommentsUrl',
            'deliveringEnclosures'  => 'getEnclosures',
            'withGuid'              => 'getGuid',
            'andGuidIsNotPermaLink' => 'isPermaLink',
            'publishedOn'           => 'getPubDate',
            'inspiredBySources'     => 'getSources',
            'withContent'           => 'getContent'
    ];
    /**
     * title of the item
     *
     * @var  string
     */
    private $title       = '';
    /**
     * URL of the item
     *
     * @var  string
     */
    private $link        = '';
    /**
     * item synopsis
     *
     * @var  string
     */
    private $description = '';
    /**
     * email address of the author of the item
     *
     * @var  string|null
     */
    private $author      = null;
    /**
     * categories where the item is included
     *
     * @var  array<array<string,string>>
     */
    private $categories  = [];
    /**
     * URL of a page for comments relating to the item
     *
     * @var  string|null
     */
    private $comments    = null;
    /**
     * describes a media object that is attached to the item
     *
     * @var  array<array<string,string>>
     */
    private $enclosures  = [];
    /**
     * unique identifier for the item
     *
     * @var  string|null
     */
    private $guid        = null;
    /**
     * whether the id may be interpreted as a permanent link or not
     *
     * @var  bool
     */
    private $isPermaLink = false;
    /**
     * indicates when the item was published
     *
     * @var  \stubbles\date\Date|null
     */
    private $pubDate     = null;
    /**
     * where that the item came from
     *
     * @var  array<array<string,string>>
     */
    private $sources     = [];
    /**
     * content of rss feed item
     *
     * @var  string|null
     */
    private $content     = null;

    /**
     * constructor
     *
     * @param  string  $title        title of the item
     * @param  string  $link         URL of the item
     * @param  string  $description  item synopsis
     */
    public function __construct(string $title, string $link, string $description)
    {
        $this->title       = $title;
        $this->link        = $link;
        $this->description = $description;
    }

    /**
     * create a new stubRssFeedItem
     *
     * @param   string  $title        title of the item
     * @param   string  $link         URL of the item
     * @param   string  $description  item synopsis
     * @return  \stubbles\xml\rss\RssFeedItem
     */
    public static function create(string $title, string $link, string $description): self
    {
        return new self($title, $link, $description);
    }

    /**
     * creates a new stubRssFeedItem from given entity
     *
     * @param   object               $entity
     * @param   array<string,mixed>  $overrides
     * @return  \stubbles\xml\rss\RssFeedItem
     * @throws  \InvalidArgumentException
     * @throws  \stubbles\xml\XmlException
     */
    public static function fromEntity(object $entity, array $overrides = []): self
    {
        $annotations = annotationsOf($entity);
        if (!$annotations->contain('RssFeedItem')) {
            throw new XmlException(
                    'Class ' . get_class($entity) . ' is not annotated with @RssFeedItem.'
            );
        }

        $rssFeedItemAnnotation = $annotations->firstNamed('RssFeedItem');
        $self = new self(
                $overrides['title'] ??
                self::getRequiredAttribute(
                        $entity,
                        'title',
                        $rssFeedItemAnnotation->getTitleMethod('getTitle')
                ),
                $overrides['link'] ??
                self::getRequiredAttribute(
                        $entity,
                        'link',
                        $rssFeedItemAnnotation->getLinkMethod('getLink')
                ),
                $overrides['description'] ??
                self::getRequiredAttribute(
                        $entity,
                        'description',
                        $rssFeedItemAnnotation->getDescriptionMethod('getDescription')
                )
        );

        foreach (self::METHODS as $itemMethod => $defaultMethod) {
            if (isset($overrides[$itemMethod])) {
                $self->$itemMethod($overrides[$itemMethod]);
                continue;
            }

            if (substr($defaultMethod, 0, 3) === 'get') {
                $annotationMethod = $defaultMethod . 'Method';
            } else {
                $annotationMethod = 'get' . $defaultMethod . 'Method';
            }

            $entityMethod = $rssFeedItemAnnotation->$annotationMethod($defaultMethod);
            if (method_exists($entity, $entityMethod)) {
                $self->$itemMethod($entity->$entityMethod());
            }
        }

        return $self;
    }

    /**
     * helper method to retrieve a required attribute
     *
     * @param   object            $entity
     * @param   string            $name
     * @param   string            $method
     * @return  string
     * @throws  \stubbles\xml\XmlException
     */
    private static function getRequiredAttribute(
            $entity,
            string $name,
            string $method
    ) {
        if (!method_exists($entity, $method)) {
            throw new XmlException(
                    'RSSFeedItem ' . get_class($entity)
                    . ' does not offer a method named "' . $method
                    . '" to return the ' . $name . ', but ' . $name
                    . ' is required.'
            );
        }

        return $entity->$method();
    }

    /**
     * returns the title of the item
     *
     * @return  string
     */
    public function title(): string
    {
        return $this->title;
    }

    /**
     * returns the URL of the item
     *
     * @return  string
     */
    public function link(): string
    {
        return $this->link;
    }

    /**
     * returns the item synopsis
     *
     * @return  string
     */
    public function description(): string
    {
        return $this->description;
    }

    /**
     * set the email address of the author of the item who created the item
     *
     * @param   string  $author  author of rss feed item
     * @return  \stubbles\xml\rss\RssFeedItem
     */
    public function byAuthor(string $author): self
    {
        if (!strstr($author, '@')) {
            $this->author = 'nospam@example.com (' . $author . ')';
        } else {
            $this->author = $author;
        }

        return $this;
    }

    /**
     * checks if author is known
     *
     * @return  bool
     */
    public function hasAuthor(): bool
    {
        return null !== $this->author;
    }

    /**
     * returns the email address of the author of the item
     *
     * @return  string
     */
    public function author(): ?string
    {
        return $this->author;
    }

    /**
     * set one or more categories where the item is included into
     *
     * @param   string  $category  category where the item is included
     * @param   string  $domain    categorization taxonomy
     * @return  \stubbles\xml\rss\RssFeedItem
     */
    public function inCategory(string $category, string $domain = ''): self
    {
        $this->categories[] = ['category' => $category, 'domain'   => $domain];
        return $this;
    }

    /**
     * sets categories where the item is included into
     *
     * Does not consider the domain of the category.
     *
     * @param   array<string|array<string>>  $categories
     * @return  \stubbles\xml\rss\RssFeedItem
     */
    public function inCategories(array $categories): self
    {
        foreach ($categories as $category) {
            if (is_array($category)) {
                $this->inCategory($category['category'], $category['domain']);
            } else {
                $this->inCategory($category);
            }
        }

        return $this;
    }

    /**
     * returns one or more categories where the item is included into
     *
     * @return  array<array<string,string>>
     */
    public function categories(): array
    {
        return $this->categories;
    }

    /**
     * set the URL of a page for comments relating to the item
     *
     * @param   string  $comments
     * @return  \stubbles\xml\rss\RssFeedItem
     */
    public function addCommentsAt(string $comments): self
    {
        $this->comments = $comments;
        return $this;
    }

    /**
     * checks whether comments are available
     *
     * @return  bool
     */
    public function hasComments(): bool
    {
        return null !== $this->comments;
    }

    /**
     * returns the URL of a page for comments relating to the item
     *
     * @return  string
     */
    public function comments(): ?string
    {
        return $this->comments;
    }

    /**
     * add an enclosure to the item
     *
     * @param   string  $url     location of enclosure
     * @param   int     $length  length of enclosure in bytes
     * @param   string  $type    MIME type of enclosure
     * @return  \stubbles\xml\rss\RssFeedItem
     */
    public function deliveringEnclosure(string $url, int $length, string $type): self
    {
        $this->enclosures[] = [
                'url'    => $url,
                'length' => (string) $length,
                'type'   => $type
        ];
        return $this;
    }

    /**
     * sets enclosures for the item
     *
     * @param   array<array<string,string>>  $enclosures
     * @return  \stubbles\xml\rss\RssFeedItem
     */
    public function deliveringEnclosures(array $enclosures): self
    {
        $this->enclosures = $enclosures;
        return $this;
    }

    /**
     * returns the description of a media object that is attached to the item
     *
     * @return  array<array<string,string>>
     */
    public function enclosures(): array
    {
        return $this->enclosures;
    }

    /**
     * set id of rss feed item
     *
     * @param   string  $guid         the id of the item
     * @return  \stubbles\xml\rss\RssFeedItem
     */
    public function withGuid(string $guid): self
    {
        $this->guid        = $guid;
        $this->isPermaLink = true;
        return $this;
    }

    /**
     * checks if guid is available
     *
     * @return  bool
     */
    public function hasGuid(): bool
    {
        return null !== $this->guid;
    }

    /**
     * returns the unique identifier for the item
     *
     * @return  string
     */
    public function guid(): ?string
    {
        return $this->guid;
    }

    /**
     * sets whether guid is perma link or not
     *
     * @return  \stubbles\xml\rss\RssFeedItem
     */
    public function andGuidIsNotPermaLink(): self
    {
        $this->isPermaLink = false;
        return $this;
    }

    /**
     * checks whether the guid represents a perma link or not
     *
     * @return  bool
     */
    public function isGuidPermaLink(): bool
    {
        return $this->isPermaLink;
    }

    /**
     * set the date when the item was published
     *
     * @param   string|int|\stubbles\date\Date  $pubDate  publishing date of rss feed item
     * @return  \stubbles\xml\rss\RssFeedItem
     */
    public function publishedOn($pubDate): self
    {
        $this->pubDate = Date::castFrom($pubDate, 'pubDate');
        return $this;
    }

    /**
     * checks if publishing date is available
     *
     * @return  bool
     */
    public function hasPubDate(): bool
    {
        return null !== $this->pubDate;
    }

    /**
     * return the publishing date of the item
     *
     * @return  string
     */
    public function pubDate(): ?string
    {
        if (null !== $this->pubDate) {
            return $this->pubDate->format('D d M Y H:i:s O');
        }

        return null;
    }

    /**
     * set the source where that the item came from
     *
     * @param   string  $name  name of the source
     * @param   string  $url   url of the source
     * @return  \stubbles\xml\rss\RssFeedItem
     */
    public function inspiredBySource(string $name, string $url): self
    {
        $this->sources[] = ['name' => $name, 'url' => $url];
        return $this;
    }

    /**
     * sets the sources where that the item came from
     *
     * @param   array<array<string,string>>  $sources
     * @return  \stubbles\xml\rss\RssFeedItem
     */
    public function inspiredBySources(array $sources): self
    {
        $this->sources = $sources;
        return $this;
    }

    /**
     * returns where that the item came from
     *
     * @return  array<array<string,string>>
     */
    public function sources(): array
    {
        return $this->sources;
    }

    /**
     * set the content of the item
     *
     * @param   string  $content  content of rss feed item
     * @return  \stubbles\xml\rss\RssFeedItem
     */
    public function withContent(string $content): self
    {
        $this->content = $content;
        return $this;
    }

    /**
     * checks of content is available
     *
     * @return  bool
     */
    public function hasContent(): bool
    {
        return (empty($this->content) === false);
    }
    /**
     * return the content of the item
     *
     * @return  string
     */
    public function content(): ?string
    {
        return $this->content;
    }
}
