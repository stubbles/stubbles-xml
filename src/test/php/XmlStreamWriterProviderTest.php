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
use stubbles\lang;
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
    public function annotationsPresentOnSetTypesMethod()
    {
        $method = lang\reflect($this->xmlStreamWriterProvider, 'setTypes');
        $this->assertTrue($method->hasAnnotation('Inject'));
        $this->assertTrue($method->getAnnotation('Inject')->isOptional());
        $this->assertTrue($method->hasAnnotation('Named'));
        $this->assertEquals('stubbles.xml.types',
                            $method->getAnnotation('Named')->getName()
        );
    }

    /**
     * @test
     */
    public function annotationsPresentOnSetVersionMethod()
    {
        $method = lang\reflect($this->xmlStreamWriterProvider, 'setVersion');
        $this->assertTrue($method->hasAnnotation('Inject'));
        $this->assertTrue($method->getAnnotation('Inject')->isOptional());
        $this->assertTrue($method->hasAnnotation('Named'));
        $this->assertEquals('stubbles.xml.version',
                            $method->getAnnotation('Named')->getName()
        );
    }

    /**
     * @test
     */
    public function annotationsPresentOnSetEncodingMethod()
    {
        $method = lang\reflect($this->xmlStreamWriterProvider, 'setEncoding');
        $this->assertTrue($method->hasAnnotation('Inject'));
        $this->assertTrue($method->getAnnotation('Inject')->isOptional());
        $this->assertTrue($method->hasAnnotation('Named'));
        $this->assertEquals('stubbles.xml.encoding',
                            $method->getAnnotation('Named')->getName()
        );
    }

    /**
     * @test
     */
    public function isDefaultProviderForXmlStreamWriter()
    {
        $class = lang\reflect('stubbles\xml\XmlStreamWriter');
        $this->assertTrue($class->hasAnnotation('ProvidedBy'));
        $this->assertEquals('stubbles\xml\XmlStreamWriterProvider',
                            $class->getAnnotation('ProvidedBy')
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
        $this->xmlStreamWriterProvider->setTypes([])->get();
    }

    /**
     * @test
     */
    public function createDomTypeIfRequested()
    {
        $this->assertInstanceOf('stubbles\xml\DomXMLStreamWriter',
                                $this->xmlStreamWriterProvider->get('dom')
        );
    }

    /**
     * @test
     */
    public function createXmlWriterTypeIfRequested()
    {
        $this->assertInstanceOf('stubbles\xml\LibXmlStreamWriter',
                                $this->xmlStreamWriterProvider->get('xmlwriter')
        );
    }

    /**
     * @test
     */
    public function createsWriterForVersion1_0ByDefault()
    {
        $this->assertEquals('1.0',
                            $this->xmlStreamWriterProvider->get()
                                                          ->getVersion()
        );
    }

    /**
     * @test
     */
    public function setVersionTo1_1CreatesWriterForVersion1_1()
    {
        $this->assertEquals('1.1',
                            $this->xmlStreamWriterProvider->setVersion('1.1')
                                                          ->get()
                                                          ->getVersion()
        );
    }

    /**
     * @test
     */
    public function createsWriterWithUTF8EncodingByDefault()
    {
        $this->assertEquals('UTF-8',
                            $this->xmlStreamWriterProvider->get()
                                                          ->getEncoding()
        );
    }

    /**
     * @test
     */
    public function createsWriterWithChangedEncoding()
    {
        $this->assertEquals('ISO-8859-1',
                            $this->xmlStreamWriterProvider->setEncoding('ISO-8859-1')
                                                          ->get()
                                                          ->getEncoding()
        );
    }
}
