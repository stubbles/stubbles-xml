<?php
declare(strict_types=1);
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace stubbles\helper\serializer;

use ArrayIterator;
use stubbles\xml\serializer\attributes\XmlTag;

/**
 * Simple example class to test the xml serializer with object and iterator serialization.
 */
#[XmlTag('container')]
class ContainerWithIterator
{
    #[XmlTag(tagName:false,elementTagName:'item')]
    public ArrayIterator $bar;

    public function __construct()
    {
        $this->bar = new ArrayIterator(array('one', 'two', 'three'));
    }
}
