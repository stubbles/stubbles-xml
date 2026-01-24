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
use stubbles\xml\serializer\delegate\Tag;

/**
 * @since 10.1
 */
#[Attribute(Attribute::TARGET_CLASS | Attribute::TARGET_PROPERTY | Attribute::TARGET_METHOD)]
class XmlTag extends Tag
{
    public function elementTagName(): ?string
    {
        return $this->elementTagName;
    }
}
