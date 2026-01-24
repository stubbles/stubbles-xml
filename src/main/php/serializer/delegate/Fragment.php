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
 * Serializer delegate to serialize a value as xml fragment.
 *
 * @since  1.6.0
 */
class Fragment implements XmlSerializerDelegate
{
    /**
     * @param  null|string|false  $tagName               name of tag
     * @param  bool    $transformNewLineToBr  switch whether to transform line breaks to <br/> or not
     */
    public function  __construct(
        private null|string|false $tagName = null,
        private ?bool $transformNewLineToBr = false
    ) { }

    public function serialize(
        mixed $value,
        XmlSerializer $xmlSerializer,
        XmlStreamWriter $xmlWriter
    ): void {
        if (is_string($this->tagName)) {
            $xmlWriter->writeStartElement($this->tagName);
            if (!empty($value)) {
                if ($this->transformNewLineToBr) {
                    $value = str_replace('&', '&amp;', nl2br($value));
                }

                $xmlWriter->writeXmlFragment($value);
            }

            $xmlWriter->writeEndElement();
        } elseif (!empty($value)) {
            $xmlWriter->writeXmlFragment($value);
        }
    }
}
