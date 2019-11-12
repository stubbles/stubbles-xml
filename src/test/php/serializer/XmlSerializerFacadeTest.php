<?php
declare(strict_types=1);
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace stubbles\xml\serializer;
use bovigo\callmap\NewInstance;
use PHPUnit\Framework\TestCase;
use stubbles\xml\XmlStreamWriter;
use stubbles\xml\serializer\XmlSerializer;

use function bovigo\assert\assertThat;
use function bovigo\assert\predicate\equals;
use function bovigo\assert\predicate\isSameAs;
/**
 * Test for stubbles\xml\serializer\XmlSerializerFacade.
 *
 * @since  1.1.0
 * @group  xml
 * @group  xml_serializer
 */
class XmlSerializerFacadeTest extends TestCase
{
    /**
     * instance to test
     *
     * @type  XmlSerializerFacade
     */
    private $xmlSerializerFacade;
    /**
     * mocked xml serializer
     *
     * @type  \bovigo\callmap\Proxy
     */
    private $xmlSerializer;
    /**
     * mocked xml stream writer
     *
     * @type  \bovigo\callmap\Proxy
     */
    private $xmlStreamWriter;

    protected function setUp(): void
    {
        libxml_clear_errors();
        $this->xmlSerializer   = NewInstance::stub(XmlSerializer::class);
        $this->xmlStreamWriter = NewInstance::of(XmlStreamWriter::class);
        $this->xmlSerializerFacade = new XmlSerializerFacade(
                $this->xmlSerializer,
                $this->xmlStreamWriter
        );
    }

    protected function tearDown(): void
    {
        libxml_clear_errors();
    }

    /**
     * @test
     */
    public function serializeToXmlReturnsXmlString()
    {
        $this->xmlSerializer->returns(['serialize' => $this->xmlStreamWriter]);
        $this->xmlStreamWriter->returns(
                ['asXml' => '<?xml version="1.0" encoding="UTF-8"?><string>foo</string>']
        );
        assertThat(
                $this->xmlSerializerFacade->serializeToXml('foo'),
                equals('<?xml version="1.0" encoding="UTF-8"?><string>foo</string>')
        );
    }

    /**
     * @test
     */
    public function serializeToDomReturnsDOMDocument()
    {
        $domDocument = new \DOMDocument();
        $this->xmlSerializer->returns(['serialize' => $this->xmlStreamWriter]);
        $this->xmlStreamWriter->returns(['asDom' => $domDocument]);
        assertThat(
                $this->xmlSerializerFacade->serializeToDom('foo'),
                isSameAs($domDocument)
        );
    }
}
