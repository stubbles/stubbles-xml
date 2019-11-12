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
 * Simple example class to test the xml serializer with object serialization.
 *
 * @XmlNonTraversable
 */
class TraversableNonTraversable implements \IteratorAggregate
{
    public function baz(): string
    {
        return 'dummy';
    }

    /**
     *
     * @return \ArrayIterator
     * @XmlIgnore
     */
    public function getIterator(): \Traversable
    {
        return new \ArrayIterator(['foo' => 'bar']);
    }

}
