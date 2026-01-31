<?php
declare(strict_types=1);
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace stubbles\xml\serializer\delegate;

use stubbles\xml\serializer\attributes\XmlAttribute;
use stubbles\xml\XmlStreamWriter;
use stubbles\xml\serializer\XmlSerializer;
/**
 * Serializer delegate to serialize a value as attribute.
 *
 * @since  1.6.0
 */
class Attribute implements XmlSerializerDelegate
{
    public function  __construct(private string $attributeName, private bool $skipEmpty = true) { }

    /**
     * @since 10.1
     */
    public static function createFromAttribute(XmlAttribute $attribute): self
    {
        return new self($attribute->name(), $attribute->skipEmpty());
    }

    public function serialize(
        mixed $value,
        XmlSerializer $xmlSerializer,
        XmlStreamWriter $xmlWriter
    ): void {
        if (gettype($value) === 'boolean') {
            $xmlWriter->writeAttribute(
                $this->attributeName,
                $value ? 'true' : 'false'
            );
            return;
        }

        if ('' === (string) $value && $this->skipEmpty) {
            return;
        }

        $xmlWriter->writeAttribute($this->attributeName, (string) $value);
    }
}
