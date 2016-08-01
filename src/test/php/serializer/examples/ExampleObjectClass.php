<?php
declare(strict_types=1);
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  stubbles\xml
 */
namespace org\stubbles\test\xml\serializer;
/**
 * Simple example class to test the xml serializer.
 *
 * @XmlTag(tagName='foo')
 */
class ExampleObjectClass
{
    /**
     * Scalar property
     *
     * @type int
     * @XmlTag(tagName='bar')
     */
    public $bar = 42;
    /**
     * Another scalar property
     *
     * @type string
     * @XmlAttribute(attributeName='bar')
     */
    public $scalar = "test";
    /**
     * Should not be exported to XML
     *
     * @type string
     * @XmlIgnore
     */
    public $ignoreMe = 'Ignore';
}
