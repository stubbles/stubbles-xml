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
 * Simple example class to test the xml serializer with object and array serialization.
 *
 * @XmlTag(tagName='container')
 */
class ContainerWithArrayListWithoutTagName
{
    /**
     * array property
     *
     * @type  array
     * @XmlTag(tagName=false, elementTagName='item')
     */
    public $bar = array('one', 'two', 'three');
}
