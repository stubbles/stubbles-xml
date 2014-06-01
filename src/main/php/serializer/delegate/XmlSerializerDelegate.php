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
 * Interface for serializer delegates.
 *
 * @since  1.6.0
 */
interface XmlSerializerDelegate
{
    /**
     * serializes given value
     *
     * @param  mixed            $value
     * @param  XmlSerializer    $xmlSerializer  serializer in case $value is not just a scalar value
     * @param  XmlStreamWriter  $xmlWriter      xml writer to write serialized object into
     */
    public function serialize($value, XmlSerializer $xmlSerializer, XmlStreamWriter $xmlWriter);
}
