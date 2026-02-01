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
use stubbles\xml\rss\attributes\RssFeedItem as RssFeedItemAttribute;
use stubbles\xml\serializer\attributes\XmlSerializer;
use stubbles\xml\XmlException;

use function stubbles\reflect\annotationsOf;
use function stubbles\reflect\attributesOf;

/**
 * Class for a rss 2.0 feed item.
 *
 * @see  http://rssboard.org/rss-specification
 */
#[XmlSerializer(RssFeedItemSerializer::class)]
class RssFeedItem
{
    /**
     * map of methods to retrieve rss feed item data
     *
     * @deprecated will be removed with 11.0.0
     */
    private const ANNOTATION_METHODS = [
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
    private const array METHODS = [
        'byAuthor'              => 'authorMethod',
        'inCategories'          => 'categoriesMethod',
        'addCommentsAt'         => 'getCommentsUrlMethod',
        'deliveringEnclosures'  => 'enclosuresMethod',
        'withGuid'              => 'guidMethod',
        'andGuidIsNotPermaLink' => 'isPermaLinkMethod',
        'publishedOn'           => 'pubDateMethod',
        'inspiredBySources'     => 'sourcesMethod',
        'withContent'           => 'contentMethod'
    ];
    /** email address of the author of the item */
    private ?string $author = null;
    /**
     * categories where the item is included
     *
     * @var  array<array<string,string>>
     */
    private array $categories = [];
    /** URL of a page for comments relating to the item */
    private ?string $comments = null;
    /**
     * describes a media object that is attached to the item
     *
     * @var  array<array<string,string>>
     */
    private array $enclosures = [];
    /** unique identifier for the item */
    private ?string $guid = null;
    /** whether the id may be interpreted as a permanent link or not */
    private bool $isPermaLink = false;
    /** indicates when the item was published */
    private ?Date $pubDate = null;
    /**
     * where that the item came from
     *
     * @var  array<array<string,string>>
     */
    private array $sources = [];
    /** content of rss feed item */
    private ?string $content = null;

    /**
     * constructor
     *
     * @param  string  $title        title of the item
     * @param  string  $link         URL of the item
     * @param  string  $description  item synopsis
     */
    public function __construct(
        private string $title,
        private string $link,
        private string $description
    ) { }

    /**
     * create a new stubRssFeedItem
     */
    public static function create(string $title, string $link, string $description): self
    {
        return new self($title, $link, $description);
    }

    /**
     * creates a new stubRssFeedItem from given entity
     *
     * @param   array<string,mixed>  $overrides
     * @throws  XmlException
     */
    public static function fromEntity(object $entity, array $overrides = []): self
    {
        $attributes = attributesOf($entity);
        if ($attributes->contain(RssFeedItemAttribute::class)) {
            $attribute = $attributes->firstNamed(RssFeedItemAttribute::class);
            $rssFeedItem = new self(
                $overrides['title'] ?? self::getRequiredAttribute($entity, 'title', $attribute->titleMethod),
                $overrides['link'] ?? self::getRequiredAttribute($entity, 'link', $attribute->linkMethod),
                $overrides['description'] ?? self::getRequiredAttribute($entity, 'description', $attribute->descriptionMethod),
            );
            foreach (self::METHODS as $itemMethod => $property) {
                if (isset($overrides[$itemMethod])) {
                    $rssFeedItem->$itemMethod($overrides[$itemMethod]);
                    continue;
                }

                $entityMethod = $attribute->$property;
                if (method_exists($entity, $entityMethod)) {
                    $rssFeedItem->$itemMethod($entity->$entityMethod());
                }
            }

            return $rssFeedItem;
        }

        $annotations = annotationsOf($entity);
        if (!$annotations->contain('RssFeedItem')) {
            throw new XmlException(
                sprintf(
                    'Class %s is neither attributed with #[%s] nor annotated with @RssFeedItem.',
                    get_class($entity),
                    RssFeedItemAttribute::class
                )
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

        foreach (self::ANNOTATION_METHODS as $itemMethod => $defaultMethod) {
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
     * @throws  XmlException
     */
    private static function getRequiredAttribute(
        object $entity,
        string $name,
        string $method
    ) {
        if (!method_exists($entity, $method)) {
            throw new XmlException(
                sprintf(
                    'RSSFeedItem of type %s does not offer a method named "%s"'
                    . ' to return the %s, but %s is required.',
                    get_class($entity),
                    $method,
                    $name,
                    $name
                )
            );
        }

        return $entity->$method();
    }

    /**
     * returns the title of the item
     */
    public function title(): string
    {
        return $this->title;
    }

    /**
     * returns the URL of the item
     */
    public function link(): string
    {
        return $this->link;
    }

    /**
     * returns the item synopsis
     */
    public function description(): string
    {
        return $this->description;
    }

    /**
     * set the email address of the author of the item who created the item
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
     */
    public function hasAuthor(): bool
    {
        return null !== $this->author;
    }

    /**
     * returns the email address of the author of the item
     */
    public function author(): ?string
    {
        return $this->author;
    }

    /**
     * set one or more categories where the item is included into
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
     */
    public function addCommentsAt(string $comments): self
    {
        $this->comments = $comments;
        return $this;
    }

    /**
     * checks whether comments are available
     */
    public function hasComments(): bool
    {
        return null !== $this->comments;
    }

    /**
     * returns the URL of a page for comments relating to the item
     */
    public function comments(): ?string
    {
        return $this->comments;
    }

    /**
     * add an enclosure to the item
     *
     * @param  string  $url     location of enclosure
     * @param  int     $length  length of enclosure in bytes
     * @param  string  $type    MIME type of enclosure
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
     * @param  array<array<string,string>>  $enclosures
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
     */
    public function withGuid(string $guid): self
    {
        $this->guid        = $guid;
        $this->isPermaLink = true;
        return $this;
    }

    /**
     * checks if guid is available
     */
    public function hasGuid(): bool
    {
        return null !== $this->guid;
    }

    /**
     * returns the unique identifier for the item
     */
    public function guid(): ?string
    {
        return $this->guid;
    }

    /**
     * sets whether guid is perma link or not
     */
    public function andGuidIsNotPermaLink(): self
    {
        $this->isPermaLink = false;
        return $this;
    }

    /**
     * checks whether the guid represents a perma link or not
     */
    public function isGuidPermaLink(): bool
    {
        return $this->isPermaLink;
    }

    /**
     * set the date when the item was published
     */
    public function publishedOn(string|int|Date $pubDate): self
    {
        $this->pubDate = Date::castFrom($pubDate, 'pubDate');
        return $this;
    }

    /**
     * checks if publishing date is available
     */
    public function hasPubDate(): bool
    {
        return null !== $this->pubDate;
    }

    /**
     * return the publishing date of the item
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
     */
    public function inspiredBySource(string $name, string $url): self
    {
        $this->sources[] = ['name' => $name, 'url' => $url];
        return $this;
    }

    /**
     * sets the sources where that the item came from
     *
     * @param  array<array<string,string>>  $sources
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
     */
    public function withContent(string $content): self
    {
        $this->content = $content;
        return $this;
    }

    /**
     * checks of content is available
     */
    public function hasContent(): bool
    {
        return empty($this->content) === false;
    }
    /**
     * return the content of the item
     */
    public function content(): ?string
    {
        return $this->content;
    }
}
