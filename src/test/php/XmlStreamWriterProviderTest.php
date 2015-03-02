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
        $this->assertTrue(
                reflect\annotationsOfConstructor($this->xmlStreamWriterProvider)
                        ->contain('Inject')
        );

        $typesParamAnnotations = reflect\annotationsOfConstructorParameter(
                'types',
                $this->xmlStreamWriterProvider
        );
        $this->assertTrue($typesParamAnnotations->contain('Named'));
        $this->assertEquals(
                'stubbles.xml.types',
                $typesParamAnnotations->firstNamed('Named')->getName()
        );
        $versionParamAnnotations = reflect\annotationsOfConstructorParameter(
                'version',
                $this->xmlStreamWriterProvider
        );
        $this->assertTrue($versionParamAnnotations->contain('Named'));
        $this->assertEquals(
                'stubbles.xml.version',
                $versionParamAnnotations->firstNamed('Named')->getName()
        );
        $encodingParamAnnotations = reflect\annotationsOfConstructorParameter(
                'encoding',
                $this->xmlStreamWriterProvider
        );
        $this->assertTrue($encodingParamAnnotations->contain('Named'));
        $this->assertEquals(
                'stubbles.xml.encoding',
                $encodingParamAnnotations->firstNamed('Named')->getName()
        );
    }

    /**
     * @test
     */
    public function isDefaultProviderForXmlStreamWriter()
    {
        $this->assertEquals(
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
            $this->assertInstanceOf('stubbles\xml\DomXmlStreamWriter',
                                    $this->xmlStreamWriterProvider->get()
            );
        } elseif (extension_loaded('xmlwriter')) {
            $this->assertInstanceOf('stubbles\xml\LibXmlStreamWriter',
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
        $this->assertInstanceOf(
                'stubbles\xml\DomXMLStreamWriter',
                $this->xmlStreamWriterProvider->get('dom')
        );
    }

    /**
     * @test
     */
    public function createXmlWriterTypeIfRequested()
    {
        $this->assertInstanceOf(
                'stubbles\xml\LibXmlStreamWriter',
                $this->xmlStreamWriterProvider->get('xmlwriter')
        );
    }

    /**
     * @test
     */
    public function createsWriterForVersion1_0ByDefault()
    {
        $this->assertEquals(
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
        $this->assertEquals(
                '1.1',
                $xmlStreamWriterProvider->get()->getVersion()
        );
    }

    /**
     * @test
     */
    public function createsWriterWithUTF8EncodingByDefault()
    {
        $this->assertEquals(
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
        $this->assertEquals(
                'ISO-8859-1',
                $xmlStreamWriterProvider->get()->getEncoding()
        );
    }
}
