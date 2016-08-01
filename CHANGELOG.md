6.0.0 (2016-08-??)
------------------

### BC breaks

  * raised minimum required PHP version to 7.0.0
  * introduced scalar type hints and strict type checking
  * dropped `get` from all getter methods of `stubbles\xml\rss\RssFeed`
  * dropped `get` from all getter methods of `stubbles\xml\rss\RssFeedItem`


5.0.0 (2016-07-05)
------------------

### BC breaks

  * raised minimum required PHP version to 5.6


4.3.0 (2015-05-28)
------------------

  * upgraded stubbles/core to 6.0


4.2.2 (2015-05-12)
------------------

  * fixed bug: respect `@XmlTag` on instances of `\Traversable`


4.2.1 (2015-05-07)
------------------

  * added possibility to annotate instances of `\Traversable` with `@XmlNonTraversable` to retain old behaviour


4.2.0 (2015-03-09)
------------------

  ### BC breaks

  * XML serializer now serializes all `\Traversable` as array, not only `\Iterator`

### Other changes

  * upgraded to stubbles/core 5.3


4.1.0 (2014-09-29)
------------------

  * upgraded stubbles/core to 5.1


4.0.0 (2014-08-17)
------------------

  * upgraded stubbles/core to 5.x
  * upgraded stubbles/date to 5.x


3.0.0 (2014-07-31)
------------------

### BC breaks

  * removed namespace prefix `net`, base namespace is now `stubbles\xml` only

### Other changes

  * upgraded to stubbles/core 4.x


2.1.0 (2013-05-02)
------------------

  * upgraded stubbles/core to ~3.0


2.0.1 (2013-02-06)
------------------

  * change dependency to stubbles-core from 2.1.* to ~2.1


2.0.0 (2012-09-06)
------------------

  * Initial release.
