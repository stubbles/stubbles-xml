<?php
declare(strict_types=1);
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace stubbles\xml\xsl;

use bovigo\callmap\ClassProxy;
use bovigo\callmap\NewInstance;
use DOMDocument;
use PHPUnit\Framework\TestCase;
use org\bovigo\vfs\vfsStream;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\RequiresPhpExtension;
use PHPUnit\Framework\Attributes\Test;
use stubbles\helper\xsl\ExtendedXslProcessor;
use stubbles\helper\xsl\XslExampleCallback;
use XSLTProcessor;

use function bovigo\assert\assertThat;
use function bovigo\assert\assertTrue;
use function bovigo\assert\expect;
use function bovigo\assert\predicate\equals;
use function bovigo\assert\predicate\isInstanceOf;
use function bovigo\assert\predicate\isOfSize;
use function bovigo\assert\predicate\isSameAs;
use function bovigo\callmap\verify;
use function stubbles\reflect\annotationsOf;
/**
 * Test for stubbles\xml\xsl\XslProcessor.
 */
#[Group('xml')]
#[Group('xml_xsl')]
#[RequiresPhpExtension('xsl')]
class XslProcessorTest extends TestCase
{
    private ExtendedXslProcessor $xslProcessor;
    private XSLTProcessor&ClassProxy $baseXsltProcessor;
    private DOMDocument $document;
    private string $stylesheet = '<xsl:stylesheet version="1.0"
    xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

  <xsl:template match="*|/">
    Intentionally not much content.
  </xsl:template>

</xsl:stylesheet>';

    protected function setUp(): void
    {
        libxml_clear_errors();
        $this->baseXsltProcessor = NewInstance::of(\XSLTProcessor::class);
        ExtendedXslProcessor::$xsltProcessor = $this->baseXsltProcessor;
        $this->xslProcessor = new ExtendedXslProcessor(new XslCallbacks());
        $this->document     = new \DOMDocument();
        $this->document->loadXML('<?xml version="1.0" encoding="UTF-8"?><foo><bar/></foo>');
        $this->xslProcessor->onDocument($this->document);
    }

    protected function tearDown(): void
    {
        libxml_clear_errors();
    }

    #[Test]
    public function providedByXslProcessorProvider(): void
    {
        assertThat(
            annotationsOf(XslProcessor::class)
                ->firstNamed('ProvidedBy')
                ->getProviderClass()
                ->getName(),
            equals(XslProcessorProvider::class)
        );
    }

    #[Test]
    #[Group('bug165')]
    public function enableProfilingBySettingPathToProfileDataFile(): void
    {
        vfsStream::setup();
        assertThat(
            $this->xslProcessor->enableProfiling(vfsStream::url('root/profile.txt')),
            isSameAs($this->xslProcessor)
        );
        verify($this->baseXsltProcessor, 'setProfiling')
            ->received(vfsStream::url('root/profile.txt'));
    }

    #[Test]
    public function onDocumentReturnsItself(): void
    {
        assertThat(
            $this->xslProcessor->onDocument($this->document),
            isSameAs($this->xslProcessor)
        );
    }

    #[Test]
    public function onXmlFileLoadsDocument(): void
    {
        $root = vfsStream::setup();
        vfsStream::newFile('test.xsl')
                ->withContent('<xsl:stylesheet version="1.0"
    xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

  <xsl:template match="*|/">
    Intentionally not much content.
  </xsl:template>

</xsl:stylesheet>')
            ->at($root);
        assertThat(
            $this->xslProcessor->onXmlFile(vfsStream::url('root/test.xsl')),
            isSameAs($this->xslProcessor)
        );
    }

    #[Test]
    public function onXMLFileThrowsIoExceptionIfFileDoesNotExist(): void
    {
        vfsStream::setup();
        expect(function() {
            $this->xslProcessor->onXmlFile(vfsStream::url('root/doesNotExist.xsl'));
        })->throws(XslProcessorException::class);
    }

    #[Test]
    public function applyStylesheetStoresStylesheet(): void
    {
        $stylesheet = new \DOMDocument();
        $stylesheet->loadXML($this->stylesheet);
        assertThat(
            $this->xslProcessor->applyStylesheet($stylesheet)
                ->getStylesheets(),
            equals([$stylesheet])
        );
    }

    #[Test]
    public function applyStylesheetFromFileStoresStylesheet(): void
    {
        $root = vfsStream::setup();
        vfsStream::newFile('test.xsl')
            ->withContent($this->stylesheet)
            ->at($root);
        assertThat(
            $this->xslProcessor
                ->applyStylesheetFromFile(vfsStream::url('root/test.xsl'))
                ->getStylesheets(),
            isOfSize(1)
        );
    }

    #[Test]
    public function failingToImportStylesheetFromFileThrowsIOException(): void
    {
        vfsStream::setup();
        expect(function() {
            $this->xslProcessor->applyStylesheetFromFile(vfsStream::url('root/doesNotExist.xsl'));
        })->throws(XslProcessorException::class);
    }

    #[Test]
    public function singleParameters(): void
    {
        $this->baseXsltProcessor->returns(['setParameter' => true]);
        $this->xslProcessor->withParameter('foo', 'bar', 'baz')
            ->withParameter('foo', 'foo', 'bar');
        verify($this->baseXsltProcessor, 'setParameter')
            ->receivedOn(1, 'foo', 'bar', 'baz');
        verify($this->baseXsltProcessor, 'setParameter')
            ->receivedOn(2, 'foo', 'foo', 'bar');
    }

    #[Test]
    public function failingToAddSingleParametersThrowsXSLProcessorException(): void
    {
        $this->baseXsltProcessor->returns(['setParameter' => false]);
        expect(function() {
            $this->xslProcessor->withParameter('foo', 'bar', 'baz');
        })->throws(XslProcessorException::class);
    }

    #[Test]
    public function arrayParameters(): void
    {
        $this->baseXsltProcessor->returns(['setParameter' => true]);
        $this->xslProcessor->withParameters('baz', ['baz' => 'bar'])
            ->withParameters('baz', ['foo' => 'bar']);
        verify($this->baseXsltProcessor, 'setParameter')
            ->receivedOn(1, 'baz', ['baz' => 'bar']);
        verify($this->baseXsltProcessor, 'setParameter')
            ->receivedOn(2, 'baz', ['foo' => 'bar']);
    }

    #[Test]
    public function failingToAddListOfParametersThrowsXSLProcessorException(): void
    {
        $this->baseXsltProcessor->returns(['setParameter' => false]);
        expect(function() {
            $this->xslProcessor->withParameters('baz', ['bar' => 'baz']);
        })->throws(XslProcessorException::class);
    }

    #[Test]
    public function cloneInstanceCopiesParameters(): void
    {
        $anotherBaseXsltProcessor = NewInstance::of(XSLTProcessor::class);
        ExtendedXslProcessor::$xsltProcessor = $anotherBaseXsltProcessor;
        $this->xslProcessor->withParameter('foo', 'bar', 'baz');
        $this->baseXsltProcessor->returns(['importStylesheet' => true]);
        $anotherBaseXsltProcessor->returns(['importStylesheet' => true]);
        $this->xslProcessor->applyStylesheet(new DOMDocument());
        $clonedXSLProcessor = clone $this->xslProcessor;
        verify($anotherBaseXsltProcessor, 'setParameter')
            ->received('foo', ['bar' => 'baz']);
    }

    #[Test]
    public function cloneInstanceCopiesStylesheets(): void
    {
        $anotherBaseXsltProcessor = NewInstance::of(XSLTProcessor::class);
        ExtendedXslProcessor::$xsltProcessor = $anotherBaseXsltProcessor;
        $stylesheet = new \DOMDocument();
        $stylesheet->loadXML($this->stylesheet);
        $this->baseXsltProcessor->returns(['importStylesheet' => true]);
        $anotherBaseXsltProcessor->returns(['importStylesheet' => true]);
        $this->xslProcessor->applyStylesheet($stylesheet);
        $clonedXSLProcessor = clone $this->xslProcessor;
        verify($anotherBaseXsltProcessor, 'importStylesheet')
            ->received($stylesheet);
    }

    #[Test]
    public function cloneInstanceDoesNotCopyDocumentToTransform(): void
    {
      ExtendedXslProcessor::$xsltProcessor = NewInstance::of(XSLTProcessor::class)
            ->returns(['importStylesheet' => true]);
        $this->baseXsltProcessor->returns(['importStylesheet' => true]);
        $this->xslProcessor->applyStylesheet(new \DOMDocument());
        $clonedXSLProcessor = clone $this->xslProcessor;
        expect(function() use($clonedXSLProcessor) {
            $clonedXSLProcessor->toDoc();
        })->throws(XslProcessorException::class);
    }

    #[Test]
    public function transformToDocWithoutDocThrowsXslProcessorException(): void
    {
        $xslProcessor = new XslProcessor(new XslCallbacks());
        expect(fn() => $xslProcessor->toDoc())
            ->throws(XslProcessorException::class);
    }

    #[Test]
    public function transformToDocReturnsDOMDocument(): void
    {
        $result = new \DOMDocument();
        $result->loadXML('<?xml version="1.0" encoding="UTF-8"?><foo><bar/></foo>');
        $this->baseXsltProcessor->returns(['transformToDoc' => $result]);
        assertThat($this->xslProcessor->toDoc(), isInstanceOf(\DOMDocument::class));
    }

    #[Test]
    public function failingTransformationToDomDocumentThrowsXSLProcessorException(): void
    {
        $this->baseXsltProcessor->returns(['transformToDoc' => false]);
        expect(function() {
            $this->xslProcessor->toDoc();
        })->throws(XslProcessorException::class);
    }

    #[Test]
    public function transformToUriWithoutDocThrowsXslProcessorException(): void
    {
        $xslProcessor = new XslProcessor(new XslCallbacks());
        expect(function() use ($xslProcessor) { $xslProcessor->toUri('foo'); })
            ->throws(XslProcessorException::class);
    }

    #[Test]
    public function transformToUri(): void
    {
        $this->baseXsltProcessor->returns(['transformToUri' => 4555]);
        assertThat($this->xslProcessor->toUri('foo'), equals(4555));
    }

    #[Test]
    public function failingTransformationToUriThrowsXSLProcessorException(): void
    {
        $this->baseXsltProcessor->returns(['transformToUri' => false]);
        expect(function() {
            $this->xslProcessor->toUri('foo');
        })->throws(XslProcessorException::class);
    }

    #[Test]
    public function transformToXmlWithoutDocThrowsXslProcessorException(): void
    {
        $xslProcessor = new XslProcessor(new XslCallbacks());
        expect(function() use ($xslProcessor) { $xslProcessor->toXml(); })
            ->throws(XslProcessorException::class);
    }

    #[Test]
    public function transformToXmlReturnsTransformedXml(): void
    {
        $this->baseXsltProcessor->returns(['transformToXml' => '<foo>']);
        assertThat($this->xslProcessor->toXML(), equals('<foo>'));
    }

    #[Test]
    public function failingTransformationToXmlThrowsXSLProcessorException(): void
    {
        $this->baseXsltProcessor->returns(['transformToXml' => false]);
        expect(function() {
            $this->xslProcessor->toXml();
        })->throws(XslProcessorException::class);
    }

    #[Test]
    public function tooLessParamsForCallbackInvocationThrowsCallbackException(): void
    {
        expect(function() { XslProcessor::invokeCallback(); })
            ->throws(XslCallbackException::class);
    }

    #[Test]
    public function invokesCorrectCallback(): void
    {
        $callback     = new XslExampleCallback();
        $xslProcessor = new ExtendedXslProcessor(new XslCallbacks());
        $xslProcessor->usingCallback('foo', $callback);
        $xslProcessor->callRegisterCallbacks();
        XslProcessor::invokeCallback('foo', 'youCanDoThis');
        assertTrue($callback->calledYouCanDoThis());
    }

    #[Test]
    public function passesParametersToCallback(): void
    {
        $callback     = new XslExampleCallback();
        $xslProcessor = new ExtendedXslProcessor(new XslCallbacks());
        $xslProcessor->usingCallback('foo', $callback);
        $xslProcessor->callRegisterCallbacks();
        XslProcessor::invokeCallback('foo', 'hello', 'mikey');
        assertThat($callback->getHelloArg(), equals('mikey'));
    }
}
