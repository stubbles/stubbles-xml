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
 * Simple example class to test the xml serializer and static properties/methods.
 */
class ExampleStaticClass
{
    /**
     * static property
     *
     * @type  string
     */
    public static $foo = 'foo';

    /**
     * static method
     *
     * @return  string
     */
    public static function getBar()
    {
        return 'bar';
    }
}
