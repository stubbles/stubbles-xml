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
 * Simple example class to test the xml serializer with an annotated serializer class.
 *
 * @XmlSerializer(org\stubbles\test\xml\serializer\ExampleObjectSerializer.class)
 */
class ExampleObjectClassWithSerializer
{
    /**
     * a property
     *
     * @type  int
     */
    public $bar    = 303;
    /**
     * another property
     *
     * @type  string
     */
    public $scalar = 'not interesting';

    /**
     * returns something
     *
     * @return  string
     */
    public function getSomething(): string
    {
        return 'something';
    }
}
