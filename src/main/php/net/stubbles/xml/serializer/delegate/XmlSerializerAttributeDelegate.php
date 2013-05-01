<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  net\stubbles\xml
 */
namespace net\stubbles\xml\serializer\delegate;
use net\stubbles\xml\XmlStreamWriter;
use net\stubbles\xml\serializer\XmlSerializer;
/**
 * Serializer delegate to serialize a value as attribute.
 *
 * @since  1.6.0
 */
class XmlSerializerAttributeDelegate implements XmlSerializerDelegate
{
    /**
     * name of attribute
     *
     * @type  string
     */
    protected $attributeName;
    /**
     * switch whether to skip serialisation if value is empty
     *
     * @type  bool
     */
    protected $skipEmpty;

    /**
     * constructor
     *
     * @param  string  $attributeName  name of attribute
     * @param  bool    $skipEmpty      switch whether to skip serialisation if value is empty
     */
    public function  __construct($attributeName, $skipEmpty)
    {
        $this->attributeName = $attributeName;
        $this->skipEmpty     = $skipEmpty;
    }

    /**
     * serializes given value
     *
     * @param  mixed            $value
     * @param  XmlSerializer    $xmlSerializer  serializer in case $value is not just a scalar value
     * @param  XmlStreamWriter  $xmlWriter      xml writer to write serialized object into
     */
    public function serialize($value, XmlSerializer $xmlSerializer, XmlStreamWriter $xmlWriter)
    {
        if (gettype($value) === 'boolean') {
            $xmlWriter->writeAttribute($this->attributeName, ((true === $value) ? ('true') : ('false')));
            return;
        }

        if ('' === (string) $value && true === $this->skipEmpty) {
            return;
        }

        $xmlWriter->writeAttribute($this->attributeName, (string) $value);
    }
}
?>