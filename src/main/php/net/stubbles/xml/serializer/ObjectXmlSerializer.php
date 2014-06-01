<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  net\stubbles\xml
 */
namespace net\stubbles\xml\serializer;
use net\stubbles\xml\XmlStreamWriter;
/**
 * Interface for object serializers.
 *
 * @since  1.6.0
 */
interface ObjectXmlSerializer
{
    /**
     * serializes given value
     *
     * @param  mixed            $object
     * @param  XmlSerializer    $xmlSerializer  serializer in case $value is not just a scalar value
     * @param  XmlStreamWriter  $xmlWriter      xml writer to write serialized object into
     * @param  string           $tagName        name of the surrounding xml tag
     */
    public function serialize($object, XmlSerializer $xmlSerializer, XmlStreamWriter $xmlWriter, $tagName);
}

