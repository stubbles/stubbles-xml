<?php
declare(strict_types=1);
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace stubbles\xml\serializer\attributes;

use Attribute;

/**
 * @since 10.1
 */
#[Attribute(Attribute::TARGET_CLASS | Attribute::TARGET_PROPERTY | Attribute::TARGET_METHOD)]
class XmlTag
{
    /**
     * @param null|string|false $tagName name of tag
     * @param ?string $elementTagName recurring element tag name for lists
     */
    public function  __construct(
        protected null|string|false $tagName = null,
        protected ?string $elementTagName = null
    ) { }

    public function tagName(): null|string|false
    {
        return $this->tagName;
    }

    public function elementTagName(): ?string
    {
        return $this->elementTagName;
    }
}
