<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  net\stubbles\xml
 */
namespace org\stubbles\test\xml\serializer;
/**
 * Simple example class to test the xml serializer with serialization of methods.
 *
 * @XmlTag(tagName='class')
 */
class ExampleObjectClassWithMethods
{
    /**
     * constructor
     */
    public function __construct()
    {
        // intentionally empty
    }

    /**
     * destructor
     */
    public function __destruct()
    {
        // intentionally empty
    }

    /**
     * another magic method
     *
     * @param  string  $prop
     */
    public function __get($prop)
    {
        // intentionally empty
    }

    /**
     * Return a value
     *
     * @return string
     * @XmlAttribute(attributeName='method')
     */
    public function getValue() {
        return "returned";
    }

    /**
     * return a boolean value
     *
     * @return  bool
     * @XmlAttribute(attributeName='isFoo')
     */
    public function isFoo()
    {
        return true;
    }

    /**
     * return a boolean value
     *
     * @return  bool
     * @XmlAttribute(attributeName='isBar')
     */
    public function isBar()
    {
        return false;
    }

    /**
     * @return  string
     */
    public function getBaz()
    {
        return 'baz';
    }

    /**
     * a method with arguments
     *
     * @param   string  $arg
     * @return  string
     */
    public function withArguments($arg)
    {
        return 'not serialized: ' . $arg;
    }
}
