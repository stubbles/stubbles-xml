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
    public static function getBar(): string
    {
        return 'bar';
    }
}
