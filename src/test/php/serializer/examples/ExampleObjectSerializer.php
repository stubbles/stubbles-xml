<?php
declare(strict_types=1);
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace org\stubbles\test\xml\serializer;
use stubbles\xml\XmlStreamWriter;
use stubbles\xml\serializer\ObjectXmlSerializer;
use stubbles\xml\serializer\XmlSerializer;
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
    public function serialize($object, XmlSerializer $xmlSerializer, XmlStreamWriter $xmlWriter, string $tagName = null)
    {
        if ($object instanceof ExampleObjectClassWithSerializer) {
            $xmlWriter->writeStartElement('example');
            $xmlWriter->writeAttribute('sound', (string) $object->bar);
            $xmlWriter->writeElement('anything', array(), $object->getSomething());
        }
    }
}
