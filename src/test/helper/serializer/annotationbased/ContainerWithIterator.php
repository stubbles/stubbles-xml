<?php
declare(strict_types=1);
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace stubbles\helper\serializer\annotationbased;

use ArrayIterator;

/**
 * Simple example class to test the xml serializer with object and iterator serialization.
 *
 * @XmlTag(tagName='container')
 */
class ContainerWithIterator
{
    /**
     * array property
     *
     * @XmlTag(tagName=false, elementTagName='item')
     */
    public ArrayIterator $bar;

    /**
     * constructor
     */
    public function __construct()
    {
        $this->bar = new ArrayIterator(array('one', 'two', 'three'));
    }
}
