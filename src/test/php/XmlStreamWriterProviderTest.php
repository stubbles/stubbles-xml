<?php
declare(strict_types=1);
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace stubbles\xml;

use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

use function bovigo\assert\assertThat;
use function bovigo\assert\assertTrue;
use function bovigo\assert\expect;
use function bovigo\assert\predicate\equals;
use function bovigo\assert\predicate\isInstanceOf;
use function stubbles\reflect\annotationsOf;
use function stubbles\reflect\annotationsOfConstructorParameter;
/**
 * Test for stubbles\xml\XmlStreamWriterProvider.
 */
#[Group('xml')]
#[Group('xml_core')]
class XmlStreamWriterProviderTest extends TestCase
{
    private XmlStreamWriterProvider $xmlStreamWriterProvider;

    protected function setUp(): void
    {
        $this->xmlStreamWriterProvider = new XmlStreamWriterProvider();
    }

    #[Test]
    public function annotationsPresentOnConstructor(): void
    {
        $typesParamAnnotations = annotationsOfConstructorParameter(
            'types',
            $this->xmlStreamWriterProvider
        );
        assertTrue($typesParamAnnotations->contain('Named'));
        assertThat(
            $typesParamAnnotations->firstNamed('Named')->getName(),
            equals('stubbles.xml.types')
        );
        $versionParamAnnotations = annotationsOfConstructorParameter(
            'version',
            $this->xmlStreamWriterProvider
        );
        assertTrue($versionParamAnnotations->contain('Named'));
        assertThat(
            $versionParamAnnotations->firstNamed('Named')->getName(),
            equals('stubbles.xml.version')
        );
        $encodingParamAnnotations = annotationsOfConstructorParameter(
            'encoding',
            $this->xmlStreamWriterProvider
        );
        assertTrue($encodingParamAnnotations->contain('Named'));
        assertThat(
            $encodingParamAnnotations->firstNamed('Named')->getName(),
            equals('stubbles.xml.encoding')
        );
    }

    #[Test]
    public function isDefaultProviderForXmlStreamWriter(): void
    {
        assertThat(
            annotationsOf(XmlStreamWriter::class)
                ->firstNamed('ProvidedBy')
                ->getProviderClass()
                ->getName(),
            equals(XmlStreamWriterProvider::class)
        );
    }

    #[Test]
    public function noSpecificRequestedTypeShouldCreateFirstAvailableType(): void
    {
        if (extension_loaded('dom')) {
            assertThat(
                $this->xmlStreamWriterProvider->get(),
                isInstanceOf(DomXmlStreamWriter::class)
            );
        } elseif (extension_loaded('xmlwriter')) {
            assertThat(
                $this->xmlStreamWriterProvider->get(),
                isInstanceOf(LibXmlStreamWriter::class)
            );
        }
    }

    #[Test]
    public function noTypeAvailableThrowsException(): void
    {
        $xmlStreamWriterProvider = new XmlStreamWriterProvider([]);
        expect(function() use($xmlStreamWriterProvider) {
            $xmlStreamWriterProvider->get();
        })->throws(XmlException::class);
    }

    #[Test]
    public function createDomTypeIfRequested(): void
    {
        assertThat(
            $this->xmlStreamWriterProvider->get('dom'),
            isInstanceOf(DomXmlStreamWriter::class)
        );
    }

    #[Test]
    public function createXmlWriterTypeIfRequested(): void
    {
        assertThat(
            $this->xmlStreamWriterProvider->get('xmlwriter'),
            isInstanceOf(LibXmlStreamWriter::class)
        );
    }

    #[Test]
    public function createsWriterForVersion1_0ByDefault(): void
    {
        assertThat(
            $this->xmlStreamWriterProvider->get()->version(),
            equals('1.0')
        );
    }

    #[Test]
    public function setVersionTo1_1CreatesWriterForVersion1_1(): void
    {
        $xmlStreamWriterProvider = new XmlStreamWriterProvider(null, '1.1');
        assertThat(
            $xmlStreamWriterProvider->get()->version(),
            equals('1.1')
        );
    }

    #[Test]
    public function createsWriterWithUTF8EncodingByDefault(): void
    {
        assertThat(
            $this->xmlStreamWriterProvider->get()->encoding(),
            equals('UTF-8')
        );
    }

    #[Test]
    public function createsWriterWithChangedEncoding(): void
    {
        $xmlStreamWriterProvider = new XmlStreamWriterProvider(null, '1.0', 'ISO-8859-1');
        assertThat(
            $xmlStreamWriterProvider->get()->encoding(),
            equals('ISO-8859-1')
        );
    }
}
