Stubbles XML related classes
============================

Preconditions for any installation
----------------------------------

Stubbles XML is meant to be used as composer package. If you are not familiar
with Composer, see [Composer - Package Management for PHP](https://github.com/composer/composer#readme).

Stubbles XML requires PHP 5.3.


Usage as library
----------------
1. In your application or dependent library, create a _composer.json_ file.
2. In the _requirements_ section, add the following dependency: `"stubbles/xml": "2.0.0-dev"`
3. Run Composer to get Stubbles XML: `php composer.phar install`
4. Run `php vendor/bin/bootstrap`. This will copy the required _bootstrap.php_ to the project`s root dir.


Installation from source
------------------------

1. Download the [`composer.phar`](http://getcomposer.org/composer.phar) executable
2. Run `git clone https://github.com/stubbles/stubbles-xml.git`
3. cd into your checkout
4.  Run Composer to get the dependencies: `php path/to/composer.phar install`

You should now be able to run the unit tests with `phpunit`.

Build status
------------

[![Build Status](https://secure.travis-ci.org/stubbles/stubbles-xml.png)](http://travis-ci.org/stubbles/stubbles-xml)
