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
use net\stubbles\lang\reflect\BaseReflectionClass;
use net\stubbles\lang\reflect\ReflectionObject;
use net\stubbles\lang\types\Date;
use net\stubbles\xml\XmlException;
use net\stubbles\xml\XmlStreamWriter;
/**
 * Class for a rss 2.0 feed item.
 *
 * @see  http://rssboard.org/rss-specification
 * @XmlSerializer(net\stubbles\xml\rss\RssFeedItemSerializer.class)
 */
class RssFeedItem extends BaseObject
{
    /**
     * title of the item
     *
     * @type  string
     */
    protected $title       = '';
    /**
     * URL of the item
     *
     * @type  string
     */
    protected $link        = '';
    /**
     * item synopsis
     *
     * @type  string
     */
    protected $description = '';
    /**
     * email address of the author of the item
     *
     * @type  string
     */
    protected $author      = null;
    /**
     * categories where the item is included
     *
     * @type  array
     */
    protected $categories  = array();
    /**
     * URL of a page for comments relating to the item
     *
     * @type  string
     */
    protected $comments    = null;
    /**
     * describes a media object that is attached to the item
     *
     * @type  array
     */
    protected $enclosures  = array();
    /**
     * unique identifier for the item
     *
     * @type  string
     */
    protected $guid        = null;
    /**
     * whether the id may be interpreted as a permanent link or not
     *
     * @type  bool
     */
    protected $isPermaLink = false;
    /**
     * indicates when the item was published
     *
     * @type  string
     */
    protected $pubDate     = null;
    /**
     * where that the item came from
     *
     * @type  array
     */
    protected $sources     = array();
    /**
     * content of rss feed item
     *
     * @type  string
     */
    protected $content     = null;

    /**
     * constructor
     *
     * @param  string  $title        title of the item
     * @param  string  $link         URL of the item
     * @param  string  $description  item synopsis
     */
    private function __construct($title, $link, $description)
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
     * @return  RssFeedItem
     */
    public static function create($title, $link, $description)
    {
        return new self($title, $link, $description);
    }

    /**
     * creates a new stubRssFeedItem from given entity
     *
     * @param   object  $entity
     * @param   array   $overrides
     * @return  RssFeedItem
     * @throws  IllegalArgumentException
     * @throws  XmlException
     */
    public static function fromEntity($entity, array $overrides = array())
    {
        if (!is_object($entity)) {
            throw new IllegalArgumentException('Given entity must be an object.');
        }

        $entityClass = new ReflectionObject($entity);
        if (!$entityClass->hasAnnotation('RssFeedItem')) {
            throw new XmlException('Class ' . $entityClass->getName() . ' is not annotated with @RssFeedItem.');
        }

        $rssFeedItemAnnotation = $entityClass->getAnnotation('RssFeedItem');
        $self    = new self(self::getRequiredAttribute($entity,
                                                       $entityClass,
                                                       'title',
                                                       $rssFeedItemAnnotation->getTitleMethod('getTitle'),
                                                       $overrides
                            ),
                            self::getRequiredAttribute($entity,
                                                       $entityClass,
                                                       'link',
                                                       $rssFeedItemAnnotation->getLinkMethod('getLink'),
                                                       $overrides
                            ),
                            self::getRequiredAttribute($entity,
                                                       $entityClass,
                                                       'description',
                                                       $rssFeedItemAnnotation->getDescriptionMethod('getDescription'),
                                                       $overrides
                            )
                   );

        foreach (array('byAuthor'              => 'getAuthor',
                       'inCategories'          => 'getCategories',
                       'addCommentsAt'         => 'getCommentsUrl',
                       'deliveringEnclosures'  => 'getEnclosures',
                       'withGuid'              => 'getGuid',
                       'andGuidIsNotPermaLink' => 'isPermaLink',
                       'publishedOn'           => 'getPubDate',
                       'inspiredBySources'     => 'getSources',
                       'withContent'           => 'getContent'
                 ) as $itemMethod => $defaultMethod) {
            if (isset($overrides[$itemMethod])) {
                $self->$itemMethod($overrides[$itemMethod]);
                continue;
            }

            if (substr($defaultMethod, 0, 3) === 'get') {
                $annotationMethod = $defaultMethod . 'Method';
            } else {
                $annotationMethod = 'get' . $defaultMethod . 'Method';
            }

            $entityMethod     = $rssFeedItemAnnotation->$annotationMethod($defaultMethod);
            if ($entityClass->hasMethod($entityMethod)) {
                $self->$itemMethod($entity->$entityMethod());
            }
        }

        return $self;
    }

    /**
     * helper method to retrieve a required attribute
     *
     * @param   object               $entity
     * @param   BaseReflectionClass  $entityClass
     * @param   string               $name
     * @param   string               $method
     * @param   array                $overrides
     * @return  string
     * @throws  XmlException
     */
    private static function getRequiredAttribute($entity, BaseReflectionClass $entityClass, $name, $method, array $overrides)
    {
        if (isset($overrides[$name])) {
            return $overrides[$name];
        }

        if (!$entityClass->hasMethod($method)) {
            throw new XmlException('RSSFeedItem ' . $entityClass->getName() . ' does not offer a method to return the ' . $name . ', but ' . $name . ' is required.');
        }

        return $entity->$method();
    }

    /**
     * returns the title of the item
     *
     * @return  string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * returns the URL of the item
     *
     * @return  string
     */
    public function getLink()
    {
        return $this->link;
    }

    /**
     * returns the item synopsis
     *
     * @return  string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * set the email address of the author of the item who created the item
     *
     * @param   string  $author  author of rss feed item
     * @return  RssFeedItem
     */
    public function byAuthor($author)
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
    public function hasAuthor()
    {
        return null !== $this->author;
    }

    /**
     * returns the email address of the author of the item
     *
     * @return  string
     */
    public function getAuthor()
    {
        return $this->author;
    }

    /**
     * set one or more categories where the item is included into
     *
     * @param   string  $category  category where the item is included
     * @param   string  $domain    categorization taxonomy
     * @return  RssFeedItem
     */
    public function inCategory($category, $domain = '')
    {
        $this->categories[] = array('category' => $category,
                                    'domain'   => $domain
                              );
        return $this;
    }

    /**
     * sets categories where the item is included into
     *
     * @param   array  $categories
     * @return  RssFeedItem
     */
    public function inCategories(array $categories)
    {
        $this->categories = $categories;
        return $this;
    }

    /**
     * returns one or more categories where the item is included into
     *
     * @return  array
     */
    public function getCategories()
    {
        return $this->categories;
    }

    /**
     * set the URL of a page for comments relating to the item
     *
     * @param   string  $comments
     * @return  RssFeedItem
     */
    public function addCommentsAt($comments)
    {
        $this->comments = $comments;
        return $this;
    }

    /**
     * checks whether comments are available
     *
     * @return  bool
     */
    public function hasComments()
    {
        return null !== $this->comments;
    }

    /**
     * returns the URL of a page for comments relating to the item
     *
     * @return  string
     */
    public function getComments()
    {
        return $this->comments;
    }

    /**
     * add an enclosure to the item
     *
     * @param   string  $url     location of enclosure
     * @param   int     $length  length of enclosure in bytes
     * @param   string  $type    MIME type of enclosure
     * @return  RssFeedItem
     */
    public function deliveringEnclosure($url, $length, $type)
    {
        $this->enclosures[] = array('url'    => $url,
                                    'length' => $length,
                                    'type'   => $type
                              );
        return $this;
    }

    /**
     * sets enclosures for the item
     *
     * @param   array  $enclosures
     * @return  RssFeedItem
     */
    public function deliveringEnclosures(array $enclosures)
    {
        $this->enclosures = $enclosures;
        return $this;
    }

    /**
     * returns the description of a media object that is attached to the item
     *
     * @return  array
     */
    public function getEnclosures()
    {
        return $this->enclosures;
    }

    /**
     * set id of rss feed item
     *
     * @param   string  $guid         the id of the item
     * @return  RssFeedItem
     */
    public function withGuid($guid)
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
    public function hasGuid()
    {
        return null !== $this->guid;
    }

    /**
     * returns the unique identifier for the item
     *
     * @return  string
     */
    public function getGuid()
    {
        return $this->guid;
    }

    /**
     * sets whether guid is perma link or not
     *
     * @return  RssFeedItem
     */
    public function andGuidIsNotPermaLink()
    {
        $this->isPermaLink = false;
        return $this;
    }

    /**
     * checks whether the guid represents a perma link or not
     *
     * @return  bool
     */
    public function isGuidPermaLink()
    {
        return $this->isPermaLink;
    }

    /**
     * set the date when the item was published
     *
     * @param   string|int|Date  $pubDate  publishing date of rss feed item
     * @return  RssFeedItem
     * @throws  IllegalArgumentException
     */
    public function publishedOn($pubDate)
    {
        if ($pubDate instanceof Date) {
            $pubDate = $pubDate->getTimestamp();
        } elseif (!is_int($pubDate)) {
            $pubDate = strtotime($pubDate);
            if (false === $pubDate) {
                throw new IllegalArgumentException('Argument must be a unix timestamp, a valid string representation of a time or an instance of net\\stubbles\\lang\\types\\Date.');
            }
        }

        $this->pubDate = date('D d M Y H:i:s O', $pubDate);
        return $this;
    }

    /**
     * checks if publishing date is available
     *
     * @return  bool
     */
    public function hasPubDate()
    {
        return null !== $this->pubDate;
    }

    /**
     * return the publishing date of the item
     *
     * @return  string
     */
    public function getPubDate()
    {
        return $this->pubDate;
    }

    /**
     * set the source where that the item came from
     *
     * @param   string  $name  name of the source
     * @param   string  $url   url of the source
     * @return  RssFeedItem
     */
    public function inspiredBySource($name, $url)
    {
        $this->sources[] = array('name' => $name, 'url' => $url);
        return $this;
    }

    /**
     * sets the sources where that the item came from
     *
     * @param   array  $sources
     * @return  RssFeedItem
     */
    public function inspiredBySources(array $sources)
    {
        $this->sources = $sources;
        return $this;
    }

    /**
     * returns where that the item came from
     *
     * @return  array
     */
    public function getSources()
    {
        return $this->sources;
    }

    /**
     * set the content of the item
     *
     * @param   string  $content  content of rss feed item
     * @return  RssFeedItem
     */
    public function withContent($content)
    {
        $this->content = $content;
        return $this;
    }

    /**
     * checks of content is available
     *
     * @return  bool
     */
    public function hasContent()
    {
        return (empty($this->content) === false);
    }
    /**
     * return the content of the item
     *
     * @return  string
     */
    public function getContent()
    {
        return $this->content;
    }
}
?>