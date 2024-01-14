<?php
declare(strict_types=1);
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace stubbles\xml\serializer;

use DOMDocument;
use stubbles\xml\XmlStreamWriter;
/**
 * Facade to simplify xml serializing.
 *
 * @since  1.1.0
 */
class XmlSerializerFacade
{
    /**
     * constructor
     *
     * @param  \stubbles\xml\serializer\XmlSerializer  $xmlSerializer
     * @param  \stubbles\xml\XmlStreamWriter           $xmlStreamWriter
     */
    public function __construct(
        private XmlSerializer $xmlSerializer,
        private XmlStreamWriter $xmlStreamWriter
    ) { }

    /**
     * serialize any data structure to XML
     */
    public function serializeToXml(mixed $data, ?string $tagName = null): string
    {
        return $this->xmlSerializer->serialize($data, $this->xmlStreamWriter, $tagName)
            ->asXml();
    }

    /**
     * serialize any data structure to XML
     */
    public function serializeToDom(mixed $data, ?string $tagName = null): DOMDocument
    {
        return $this->xmlSerializer->serialize($data, $this->xmlStreamWriter, $tagName)
            ->asDOM();
    }
}
