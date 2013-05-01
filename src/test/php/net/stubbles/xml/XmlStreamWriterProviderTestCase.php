<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  net\stubbles\xml
 */
namespace net\stubbles\xml;
use net\stubbles\lang\reflect\ReflectionClass;
use net\stubbles\lang\reflect\ReflectionObject;
/**
 * Test for net\stubbles\xml\XmlStreamWriterProvider.
 *
 * @group  xml
 * @group  xml_core
 */
class XmlStreamWriterProviderTestCase extends \PHPUnit_Framework_TestCase
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
        $method = ReflectionObject::fromInstance($this->xmlStreamWriterProvider)
                                  ->getMethod('setTypes');
        $this->assertTrue($method->hasAnnotation('Inject'));
        $this->assertTrue($method->getAnnotation('Inject')->isOptional());
        $this->assertTrue($method->hasAnnotation('Named'));
        $this->assertEquals('net.stubbles.xml.types',
                            $method->getAnnotation('Named')->getName()
        );
    }

    /**
     * @test
     */
    public function annotationsPresentOnSetVersionMethod()
    {
        $method = ReflectionObject::fromInstance($this->xmlStreamWriterProvider)
                                  ->getMethod('setVersion');
        $this->assertTrue($method->hasAnnotation('Inject'));
        $this->assertTrue($method->getAnnotation('Inject')->isOptional());
        $this->assertTrue($method->hasAnnotation('Named'));
        $this->assertEquals('net.stubbles.xml.version',
                            $method->getAnnotation('Named')->getName()
        );
    }

    /**
     * @test
     */
    public function annotationsPresentOnSetEncodingMethod()
    {
        $method = ReflectionObject::fromInstance($this->xmlStreamWriterProvider)
                                  ->getMethod('setEncoding');
        $this->assertTrue($method->hasAnnotation('Inject'));
        $this->assertTrue($method->getAnnotation('Inject')->isOptional());
        $this->assertTrue($method->hasAnnotation('Named'));
        $this->assertEquals('net.stubbles.xml.encoding',
                            $method->getAnnotation('Named')->getName()
        );
    }

    /**
     * @test
     */
    public function isDefaultProviderForXmlStreamWriter()
    {
        $class = new ReflectionClass('net\stubbles\xml\XmlStreamWriter');
        $this->assertTrue($class->hasAnnotation('ProvidedBy'));
        $this->assertEquals('net\stubbles\xml\XmlStreamWriterProvider',
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
            $this->assertInstanceOf('net\stubbles\xml\DomXmlStreamWriter',
                                    $this->xmlStreamWriterProvider->get()
            );
        } elseif (extension_loaded('xmlwriter')) {
            $this->assertInstanceOf('net\stubbles\xml\LibXmlStreamWriter',
                                    $this->xmlStreamWriterProvider->get()
            );
        }
    }

    /**
     * @test
     * @expectedException  net\stubbles\xml\XmlException
     */
    public function noTypeAvailableThrowsException()
    {
        $this->xmlStreamWriterProvider->setTypes(array())->get();
    }

    /**
     * @test
     */
    public function createDomTypeIfRequested()
    {
        $this->assertInstanceOf('net\stubbles\xml\DomXMLStreamWriter',
                                $this->xmlStreamWriterProvider->get('dom')
        );
    }

    /**
     * @test
     */
    public function createXmlWriterTypeIfRequested()
    {
        $this->assertInstanceOf('net\stubbles\xml\LibXmlStreamWriter',
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
?>