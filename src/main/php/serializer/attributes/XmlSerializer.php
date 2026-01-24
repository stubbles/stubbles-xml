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
use stubbles\xml\serializer\ObjectXmlSerializer;

/**
 * @since 10.1
 * @template T of ObjectXmlSerializer
 */
#[Attribute(Attribute::TARGET_CLASS)]
class XmlSerializer
{
    /**
     * @param class-string<T>
     */
    public function __construct(private string $serializerClass) { }

    /**
     * @return class-string<T>
     */
    public function getClassName(): string
    {
        return $this->serializerClass;
    }
}
