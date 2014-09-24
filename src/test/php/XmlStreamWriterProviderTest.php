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
    public function annotationsPresentOnConstructor()
    {
        $constructor = lang\reflectConstructor($this->xmlStreamWriterProvider);
        $this->assertTrue($constructor->hasAnnotation('Inject'));

        $parameters = $constructor->getParameters();
        $this->assertTrue($parameters[0]->hasAnnotation('Named'));
        $this->assertEquals(
                'stubbles.xml.types',
                $parameters[0]->annotation('Named')->getName()
        );
        $this->assertTrue($parameters[1]->hasAnnotation('Named'));
        $this->assertEquals(
                'stubbles.xml.version',
                $parameters[1]->annotation('Named')->getName()
        );
        $this->assertTrue($parameters[2]->hasAnnotation('Named'));
        $this->assertEquals(
                'stubbles.xml.encoding',
                $parameters[2]->annotation('Named')->getName()
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
