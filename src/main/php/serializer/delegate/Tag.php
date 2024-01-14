<?php
declare(strict_types=1);
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace stubbles\xml\serializer\delegate;
use stubbles\xml\XmlStreamWriter;
use stubbles\xml\serializer\XmlSerializer;
/**
 * Serializer delegate to serialize a value to a tag.
 *
 * @since  1.6.0
 */
class Tag implements XmlSerializerDelegate
{
    /**
     * @param  string  $tagName         name of tag
     * @param  string  $elementTagName  recurring element tag name for lists
     */
    public function  __construct(
        private ?string $tagName = null,
        private ?string $elementTagName = null
    ) { }

    public function serialize(
        mixed $value,
        XmlSerializer $xmlSerializer,
        XmlStreamWriter $xmlWriter
    ): void {
        $xmlSerializer->serialize($value, $xmlWriter, $this->tagName, $this->elementTagName);
    }
}
