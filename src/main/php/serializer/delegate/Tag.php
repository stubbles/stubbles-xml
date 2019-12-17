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
     * name of tag
     *
     * @var  string|null
     */
    protected $tagName;
    /**
     * recurring element tag name for lists
     *
     * @var  string|null
     */
    protected $elementTagName;

    /**
     * constructor
     *
     * @param  string  $tagName         name of tag
     * @param  string  $elementTagName  recurring element tag name for lists
     */
    public function  __construct(string $tagName = null, string $elementTagName = null)
    {
        $this->tagName        = $tagName;
        $this->elementTagName = $elementTagName;
    }

    /**
     * serializes given value
     *
     * @param  mixed                                   $value
     * @param  \stubbles\xml\serializer\XmlSerializer  $xmlSerializer  serializer in case $value is not just a scalar value
     * @param  \stubbles\xml\XmlStreamWriter           $xmlWriter      xml writer to write serialized object into
     */
    public function serialize($value, XmlSerializer $xmlSerializer, XmlStreamWriter $xmlWriter): void
    {
        $xmlSerializer->serialize($value, $xmlWriter, $this->tagName, $this->elementTagName);
    }
}
