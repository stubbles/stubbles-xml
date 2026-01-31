<?php
declare(strict_types=1);
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace stubbles\xml\serializer\delegate;

use stubbles\xml\serializer\attributes\XmlTag;
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
     * @param null|string|false $tagName name of tag
     * @param ?string $elementTagName recurring element tag name for lists
     */
    public function  __construct(
        protected null|string|false $tagName = null,
        protected ?string $elementTagName = null
    ) { }

    /**
     * @since 10.1
     */
    public static function createFromAttribute(XmlTag $attribute): self
    {
        return new self($attribute->tagName(), $attribute->elementTagName());
    }

    public function tagName(): ?string
    {
        if (false === $this->tagName) {
            return '';
        }

        return $this->tagName;
    }

    public function serialize(
        mixed $value,
        XmlSerializer $xmlSerializer,
        XmlStreamWriter $xmlWriter
    ): void {
        $xmlSerializer->serialize($value, $xmlWriter, $this->tagName(), $this->elementTagName);
    }
}
