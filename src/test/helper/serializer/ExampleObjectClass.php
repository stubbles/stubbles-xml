<?php
declare(strict_types=1);
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace stubbles\helper\serializer;
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
     * @XmlTag(tagName='bar')
     */
    public mixed $bar = 42;
    /**
     * Another scalar property
     *
     * @XmlAttribute(attributeName='bar')
     */
    public string $scalar = "test";
    /**
     * Should not be exported to XML
     *
     * @XmlIgnore
     */
    public string $ignoreMe = 'Ignore';
}
