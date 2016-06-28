<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  stubbles\xml
 */
namespace stubbles\xml\serializer;
use bovigo\callmap\NewInstance;
use stubbles\xml\XmlStreamWriter;
use stubbles\xml\serializer\XmlSerializer;

use function bovigo\assert\assert;
use function bovigo\assert\predicate\equals;
use function bovigo\assert\predicate\isSameAs;
/**
 * Test for stubbles\xml\serializer\XmlSerializerFacade.
 *
 * @since  1.1.0
 * @group  xml
 * @group  xml_serializer
 */
class XmlSerializerFacadeTest extends \PHPUnit_Framework_TestCase
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

    /**
     * set up test environment
     */
    public function setUp()
    {
        libxml_clear_errors();
        $this->xmlSerializer   = NewInstance::stub(XmlSerializer::class);
        $this->xmlStreamWriter = NewInstance::of(XmlStreamWriter::class);
        $this->xmlSerializerFacade = new XmlSerializerFacade(
                $this->xmlSerializer,
                $this->xmlStreamWriter
        );
    }

    /**
     * clean up test environment
     */
    public function tearDown()
    {
        libxml_clear_errors();
    }

    /**
     * @test
     */
    public function serializeToXmlReturnsXmlString()
    {
        $this->xmlSerializer->mapCalls(['serialize' => $this->xmlStreamWriter]);
        $this->xmlStreamWriter->mapCalls(
                ['asXml' => '<?xml version="1.0" encoding="UTF-8"?><string>foo</string>']
        );
        assert(
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
        $this->xmlSerializer->mapCalls(['serialize' => $this->xmlStreamWriter]);
        $this->xmlStreamWriter->mapCalls(['asDom' => $domDocument]);
        assert(
                $this->xmlSerializerFacade->serializeToDom('foo'),
                isSameAs($domDocument)
        );
    }
}
