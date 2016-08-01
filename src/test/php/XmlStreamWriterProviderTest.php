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
namespace stubbles\xml;
use function bovigo\assert\assert;
use function bovigo\assert\assertTrue;
use function bovigo\assert\expect;
use function bovigo\assert\predicate\equals;
use function bovigo\assert\predicate\isInstanceOf;
use function stubbles\reflect\annotationsOf;
use function stubbles\reflect\annotationsOfConstructorParameter;
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
        $typesParamAnnotations = annotationsOfConstructorParameter(
                'types',
                $this->xmlStreamWriterProvider
        );
        assertTrue($typesParamAnnotations->contain('Named'));
        assert(
                $typesParamAnnotations->firstNamed('Named')->getName(),
                equals('stubbles.xml.types')
        );
        $versionParamAnnotations = annotationsOfConstructorParameter(
                'version',
                $this->xmlStreamWriterProvider
        );
        assertTrue($versionParamAnnotations->contain('Named'));
        assert(
                $versionParamAnnotations->firstNamed('Named')->getName(),
                equals('stubbles.xml.version')
        );
        $encodingParamAnnotations = annotationsOfConstructorParameter(
                'encoding',
                $this->xmlStreamWriterProvider
        );
        assertTrue($encodingParamAnnotations->contain('Named'));
        assert(
                $encodingParamAnnotations->firstNamed('Named')->getName(),
                equals('stubbles.xml.encoding')
        );
    }

    /**
     * @test
     */
    public function isDefaultProviderForXmlStreamWriter()
    {
        assert(
                annotationsOf(XmlStreamWriter::class)
                        ->firstNamed('ProvidedBy')
                        ->getProviderClass()
                        ->getName(),
                equals(XmlStreamWriterProvider::class)
        );
    }

    /**
     * @test
     */
    public function noSpecificRequestedTypeShouldCreateFirstAvailableType()
    {
        if (extension_loaded('dom')) {
            assert(
                    $this->xmlStreamWriterProvider->get(),
                    isInstanceOf(DomXmlStreamWriter::class)
            );
        } elseif (extension_loaded('xmlwriter')) {
            assert(
                    $this->xmlStreamWriterProvider->get(),
                    isInstanceOf(LibXmlStreamWriter::class)
            );
        }
    }

    /**
     * @test
     */
    public function noTypeAvailableThrowsException()
    {
        $xmlStreamWriterProvider = new XmlStreamWriterProvider([]);
        expect(function() use($xmlStreamWriterProvider) {
                $xmlStreamWriterProvider->get();
        })->throws(XmlException::class);
    }

    /**
     * @test
     */
    public function createDomTypeIfRequested()
    {
        assert(
                $this->xmlStreamWriterProvider->get('dom'),
                isInstanceOf(DomXmlStreamWriter::class)
        );
    }

    /**
     * @test
     */
    public function createXmlWriterTypeIfRequested()
    {
        assert(
                $this->xmlStreamWriterProvider->get('xmlwriter'),
                isInstanceOf(LibXmlStreamWriter::class)
        );
    }

    /**
     * @test
     */
    public function createsWriterForVersion1_0ByDefault()
    {
        assert(
                $this->xmlStreamWriterProvider->get()->getVersion(),
                equals('1.0')
        );
    }

    /**
     * @test
     */
    public function setVersionTo1_1CreatesWriterForVersion1_1()
    {
        $xmlStreamWriterProvider = new XmlStreamWriterProvider(null, '1.1');
        assert(
                $xmlStreamWriterProvider->get()->getVersion(),
                equals('1.1')
        );
    }

    /**
     * @test
     */
    public function createsWriterWithUTF8EncodingByDefault()
    {
        assert(
                $this->xmlStreamWriterProvider->get()->getEncoding(),
                equals('UTF-8')
        );
    }

    /**
     * @test
     */
    public function createsWriterWithChangedEncoding()
    {
        $xmlStreamWriterProvider = new XmlStreamWriterProvider(null, '1.0', 'ISO-8859-1');
        assert(
                $xmlStreamWriterProvider->get()->getEncoding(),
                equals('ISO-8859-1')
        );
    }
}
