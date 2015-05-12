<?php
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
 * Simple example class to test the xml serializer with object serialization.
 *
 * @XmlTag(tagName='foo', elementTagName='example')
 */
class TraversableTraversable implements \IteratorAggregate
{
    public function baz()
    {
        return 'dummy';
    }

    /**
     *
     * @return \ArrayIterator
     * @XmlIgnore
     */
    public function getIterator()
    {
        return new \ArrayIterator(['bar']);
    }

}