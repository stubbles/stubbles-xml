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
class ContainerWithArrayListTagName
{
    /**
     * array property
     *
     * @XmlTag(tagName='list', elementTagName='item')
     */
    public array $bar = array('one', 'two', 'three');
}
