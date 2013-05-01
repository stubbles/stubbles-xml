<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  net\stubbles\xml
 */
namespace org\stubbles\test\xml\serializer;
use net\stubbles\xml\XmlStreamWriter;
use net\stubbles\xml\serializer\ObjectXmlSerializer;
use net\stubbles\xml\serializer\XmlSerializer;
/**
 * Simple example class to test the xml serializer with an annotated serializer class.
 */
class ExampleObjectSerializer implements ObjectXmlSerializer
{
    /**
     * serializes given value
     *
     * @param  mixed            $value
     * @param  XmlSerializer    $xmlSerializer  serializer in case $value is not just a scalar value
     * @param  XmlStreamWriter  $xmlWriter      xml writer to write serialized object into
     * @param  string           $tagName        name of the surrounding xml tag
     */
    public function serialize($object, XmlSerializer $xmlSerializer, XmlStreamWriter $xmlWriter, $tagName)
    {
        if ($object instanceof ExampleObjectClassWithSerializer) {
            $xmlWriter->writeStartElement('example');
            $xmlWriter->writeAttribute('sound', $object->bar);
            $xmlWriter->writeElement('anything', array(), $object->getSomething());
        }
    }
}
?>