# Changelog

## 10.1.0 (2026-??-??)

* Added attributes to replace all annotation based features. Annotations are now deprecated, and support for annotations will be removed with release 11.0.0.
  * `stubbles\xml\serializer\attributes\XmlSerializer` (replaces `@XmlSerializer`)
  * `stubbles\xml\serializer\attributes\XmlTag` (replaces `@XmlTag`)
  * `stubbles\xml\serializer\attributes\XmlAttribute` (replaces `@XmlAttribute`)
  * `stubbles\xml\serializer\attributes\XmlFragment` (replaces `@XmlFragment`)
  * `stubbles\xml\serializer\attributes\XmlIgnore` (replaces `@XmlIgnore`)
  * `stubbles\xml\serializer\attributes\XmlNonTraversable` (replaces `@XmlNonTraversable`)
  * `stubbles\xml\rss\attributes\RssFeedItem` (replaces `@RssFeedItem`)

## 10.0.0 (2025-12-07)

### BC breaks

* raised minimum required PHP version to 8.3

### Other changes

* ensured compatibility with PHP 8.5

## 9.0.0 (2024-01-15)

### BC breaks

* raised minimum required PHP version to 8.2
* added type hint `object` for first parameter of `stubbles\xml\serializer\ObjectXmlSerializer::serialize()`

### Other changes

* provide more specific types where applicable
* removed usage of deprecated features in dependencies

## 8.0.0 (2019-12-17)

### BC breaks

* added return value `void` to interface methods:
  * `stubbles\xml\serializer\ObjectXmlSerializer::serialize()`
  * `stubbles\xml\serializer\delegate\XmlSerializerDelegate::serialize()`
* added more phpstan related type hints

## 7.0.0 (2019-11-12)

### BC breaks

* raised minimum required PHP version to 7.3

## 6.0.0 (2016-08-02)

### BC breaks

* raised minimum required PHP version to 7.0.0
* introduced scalar type hints and strict type checking
* dropped `get` from all getter methods of `stubbles\xml\rss\RssFeed`
* dropped `get` from all getter methods of `stubbles\xml\rss\RssFeedItem`
* dropped `get` from all getter methods of `stubbles\xml\XmlStreamWriter`
* `stubbles\xml\XmlStreamWriter` is now abstract base class and not an interface any more, removed `stubbles\xml\AbstractXmlStreamWriter`

## 5.0.0 (2016-07-05)

### BC breaks

* raised minimum required PHP version to 5.6

## 4.3.0 (2015-05-28)

* upgraded stubbles/core to 6.0

## 4.2.2 (2015-05-12)

* fixed bug: respect `@XmlTag` on instances of `\Traversable`

## 4.2.1 (2015-05-07)

* added possibility to annotate instances of `\Traversable` with `@XmlNonTraversable` to retain old behaviour

## 4.2.0 (2015-03-09)

### BC breaks

* XML serializer now serializes all `\Traversable` as array, not only `\Iterator`

### Other changes

* upgraded to stubbles/core 5.3

## 4.1.0 (2014-09-29)

* upgraded stubbles/core to 5.1

## 4.0.0 (2014-08-17)

* upgraded stubbles/core to 5.x
* upgraded stubbles/date to 5.x

## 3.0.0 (2014-07-31)

### BC breaks

* removed namespace prefix `net`, base namespace is now `stubbles\xml` only

### Other changes

* upgraded to stubbles/core 4.x

## 2.1.0 (2013-05-02)

* upgraded stubbles/core to ~3.0

## 2.0.1 (2013-02-06)

* change dependency to stubbles-core from 2.1.* to ~2.1

## 2.0.0 (2012-09-06)

* Initial release.
