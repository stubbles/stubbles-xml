<?php
declare(strict_types=1);
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace stubbles\helper\serializer;
use stubbles\xml\XmlStreamWriter;
use stubbles\xml\serializer\ObjectXmlSerializer;
use stubbles\xml\serializer\XmlSerializer;
/**
 * Simple example class to test the xml serializer with an annotated serializer class.
 */
class ExampleObjectSerializer implements ObjectXmlSerializer
{
    public function serialize(
        mixed $object,
        XmlSerializer $xmlSerializer,
        XmlStreamWriter $xmlWriter,
        ?string $tagName = null
    ): void {
        if ($object instanceof ExampleObjectClassWithSerializer) {
            $xmlWriter->writeStartElement('example');
            $xmlWriter->writeAttribute('sound', (string) $object->bar);
            $xmlWriter->writeElement('anything', array(), $object->getSomething());
        }
    }
}
