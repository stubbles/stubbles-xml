<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  stubbles\xml
 */
namespace stubbles\xml;
use stubbles\lang\reflect;
/**
 * Test for stubbles\xml\XmlStreamWriterProvider.
 *
 * @group  xml
 * @group  xml_core
 */
class XmlStreamWriterProviderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * instance to test
     *
     * @type  XmlStreamWriterProvider
     */
    private $xmlStreamWriterProvider;

    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->xmlStreamWriterProvider = new XmlStreamWriterProvider();
    }

    /**
     * @test
     */
    public function annotationsPresentOnConstructor()
    {
        $typesParamAnnotations = reflect\annotationsOfConstructorParameter(
                'types',
                $this->xmlStreamWriterProvider
        );
        assertTrue($typesParamAnnotations->contain('Named'));
        assertEquals(
                'stubbles.xml.types',
                $typesParamAnnotations->firstNamed('Named')->getName()
        );
        $versionParamAnnotations = reflect\annotationsOfConstructorParameter(
                'version',
                $this->xmlStreamWriterProvider
        );
        assertTrue($versionParamAnnotations->contain('Named'));
        assertEquals(
                'stubbles.xml.version',
                $versionParamAnnotations->firstNamed('Named')->getName()
        );
        $encodingParamAnnotations = reflect\annotationsOfConstructorParameter(
                'encoding',
                $this->xmlStreamWriterProvider
        );
        assertTrue($encodingParamAnnotations->contain('Named'));
        assertEquals(
                'stubbles.xml.encoding',
                $encodingParamAnnotations->firstNamed('Named')->getName()
        );
    }

    /**
     * @test
     */
    public function isDefaultProviderForXmlStreamWriter()
    {
        assertEquals(
                'stubbles\xml\XmlStreamWriterProvider',
                reflect\annotationsOf('stubbles\xml\XmlStreamWriter')
                        ->firstNamed('ProvidedBy')
                        ->getProviderClass()
                        ->getName()
        );
    }

    /**
     * @test
     */
    public function noSpecificRequestedTypeShouldCreateFirstAvailableType()
    {
        if (extension_loaded('dom')) {
            assertInstanceOf(
                    'stubbles\xml\DomXmlStreamWriter',
                    $this->xmlStreamWriterProvider->get()
            );
        } elseif (extension_loaded('xmlwriter')) {
            assertInstanceOf(
                    'stubbles\xml\LibXmlStreamWriter',
                    $this->xmlStreamWriterProvider->get()
            );
        }
    }

    /**
     * @test
     * @expectedException  stubbles\xml\XmlException
     */
    public function noTypeAvailableThrowsException()
    {
        $xmlStreamWriterProvider = new XmlStreamWriterProvider([]);
        $xmlStreamWriterProvider->get();
    }

    /**
     * @test
     */
    public function createDomTypeIfRequested()
    {
        assertInstanceOf(
                'stubbles\xml\DomXMLStreamWriter',
                $this->xmlStreamWriterProvider->get('dom')
        );
    }

    /**
     * @test
     */
    public function createXmlWriterTypeIfRequested()
    {
        assertInstanceOf(
                'stubbles\xml\LibXmlStreamWriter',
                $this->xmlStreamWriterProvider->get('xmlwriter')
        );
    }

    /**
     * @test
     */
    public function createsWriterForVersion1_0ByDefault()
    {
        assertEquals(
                '1.0',
                $this->xmlStreamWriterProvider->get()->getVersion()
        );
    }

    /**
     * @test
     */
    public function setVersionTo1_1CreatesWriterForVersion1_1()
    {
        $xmlStreamWriterProvider = new XmlStreamWriterProvider(null, '1.1');
        assertEquals(
                '1.1',
                $xmlStreamWriterProvider->get()->getVersion()
        );
    }

    /**
     * @test
     */
    public function createsWriterWithUTF8EncodingByDefault()
    {
        assertEquals(
                'UTF-8',
                $this->xmlStreamWriterProvider->get()->getEncoding()
        );
    }

    /**
     * @test
     */
    public function createsWriterWithChangedEncoding()
    {
        $xmlStreamWriterProvider = new XmlStreamWriterProvider(null, '1.0', 'ISO-8859-1');
        assertEquals(
                'ISO-8859-1',
                $xmlStreamWriterProvider->get()->getEncoding()
        );
    }
}
