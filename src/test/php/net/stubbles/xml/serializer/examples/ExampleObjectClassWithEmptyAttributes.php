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
 * Simple example class to test the xml serializer with empty attribute values.
 *
 * @XmlTag(tagName='test')
 */
class ExampleObjectClassWithEmptyAttributes
{
    /**
     * Empty property
     *
     * @type mixed
     * @XmlAttribute(attributeName='emptyProp')
     */
    public $emptyProp;
    /**
     * Empty property
     *
     * @type mixed
     * @XmlAttribute(attributeName='emptyProp2', skipEmpty=false)
     */
    public $emptyProp2;

    /**
     * Empty return value
     *
     * @return mixed
     * @XmlAttribute(attributeName='emptyMethod')
     */
    public function getEmpty() {
        return null;
    }

    /**
     * Empty return value
     *
     * @return mixed
     * @XmlAttribute(attributeName='emptyMethod2', skipEmpty=false)
     */
    public function getEmpty2() {
        return null;
    }
}
