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
 * Interface for serializer delegates.
 *
 * @since  1.6.0
 */
interface XmlSerializerDelegate
{
    /**
     * serializes given value
     */
    public function serialize(
        mixed $value,
        XmlSerializer $xmlSerializer,
        XmlStreamWriter $xmlWriter
    ): void;
}
