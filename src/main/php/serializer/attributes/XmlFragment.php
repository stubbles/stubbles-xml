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
#[Attribute(Attribute::TARGET_PROPERTY | Attribute::TARGET_METHOD)]
class XmlFragment
{
    /**
     * @param null|string|false $tagName name of tag
     * @param bool $transformNewLineToBr switch whether to transform line breaks to <br/> or not
     */
    public function  __construct(
        private null|string|false $tagName = null,
        private ?bool $transformNewLineToBr = false
    ) { }

    public function tagName(): null|string|false
    {
        return $this->tagName;
    }

    public function transformNewLineToBr(): bool
    {
        return $this->transformNewLineToBr;
    }
}