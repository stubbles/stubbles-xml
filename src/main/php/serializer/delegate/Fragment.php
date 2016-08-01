<?php
declare(strict_types=1);
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  stubbles\xml
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
     * name of tag
     *
     * @type  string
     */
    protected $tagName;
    /**
     * switch whether to transform line breaks to <br/> or not
     *
     * @type  bool
     */
    protected $transformNewLineToBr;

    /**
     * constructor
     *
     * @param  string  $tagName               name of tag
     * @param  bool    $transformNewLineToBr  switch whether to transform line breaks to <br/> or not
     */
    public function  __construct(string $tagName = null, bool $transformNewLineToBr = false)
    {
        $this->tagName              = $tagName;
        $this->transformNewLineToBr = $transformNewLineToBr;
    }

    /**
     * serializes given value
     *
     * @param  mixed                                   $value
     * @param  \stubbles\xml\serializer\XmlSerializer  $xmlSerializer  serializer in case $value is not just a scalar value
     * @param  \stubbles\xml\XmlStreamWriter           $xmlWriter      xml writer to write serialized object into
     */
    public function serialize($value, XmlSerializer $xmlSerializer, XmlStreamWriter $xmlWriter)
    {
        if (null != $this->tagName) {
            $xmlWriter->writeStartElement($this->tagName);
            if (!empty($value)) {
                if (true === $this->transformNewLineToBr) {
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
