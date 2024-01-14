<?php
declare(strict_types=1);
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace stubbles\xml\serializer;
use stubbles\xml\XmlStreamWriter;
/**
 * Interface for object serializers.
 *
 * @since  1.6.0
 * @template T of object
 */
interface ObjectXmlSerializer
{
    /**
     * serializes given value
     *
     * @param  T       $object
     * @param  string  $tagName  name of the surrounding xml tag
     */
    public function serialize(
            object $object,
            XmlSerializer $xmlSerializer,
            XmlStreamWriter $xmlWriter,
            ?string $tagName = null
    ): void;
}
