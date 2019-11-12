<?php
declare(strict_types=1);
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace stubbles\xml\xsl;
use bovigo\callmap\NewInstance;
use PHPUnit\Framework\TestCase;
use org\bovigo\vfs\vfsStream;
use stubbles\helper\xsl\ExtendedXslProcessor;
use stubbles\helper\xsl\XslExampleCallback;

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
 *
 * @group     xml
 * @group     xml_xsl
 * @requires  extension  xsl
 */
class XslProcessorTest extends TestCase
{
    /**
     * instance to test
     *
     * @type  ExtendedXslProcessor
     */
    private $xslProcessor;
    /**
     * a mock for the XSLTProcessor
     *
     * @type  \bovigo\callmap\Proxy
     */
    private $baseXsltProcessor;
    /**
     * a dom document to test
     *
     * @type  \DOMDocument
     */
    private $document;
    /**
     * some stylesheet
     *
     * @type  string
     */
    private $stylesheet = '<xsl:stylesheet version="1.0"
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

    /**
     * @test
     */
    public function providedByXslProcessorProvider()
    {
        assertThat(
                annotationsOf(XslProcessor::class)
                        ->firstNamed('ProvidedBy')
                        ->getProviderClass()
                        ->getName(),
                equals(XslProcessorProvider::class)
        );
    }

    /**
     * @test
     * @group  bug165
     */
    public function enableProfilingBySettingPathToProfileDataFile()
    {
        vfsStream::setup();
        assertThat(
                $this->xslProcessor->enableProfiling(vfsStream::url('root/profile.txt')),
                isSameAs($this->xslProcessor)
        );
        verify($this->baseXsltProcessor, 'setProfiling')
                ->received(vfsStream::url('root/profile.txt'));
    }

    /**
     * @test
     */
    public function onDocumentReturnsItself()
    {
        assertThat(
                $this->xslProcessor->onDocument($this->document),
                isSameAs($this->xslProcessor)
        );
    }

    /**
     * @test
     */
    public function onXmlFileLoadsDocument()
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

    /**
     * @test
     */
    public function onXMLFileThrowsIoExceptionIfFileDoesNotExist()
    {
        vfsStream::setup();
        expect(function() {
                $this->xslProcessor->onXmlFile(vfsStream::url('root/doesNotExist.xsl'));
        })->throws(XslProcessorException::class);
    }

    /**
     * @test
     */
    public function applyStylesheetStoresStylesheet()
    {
        $stylesheet = new \DOMDocument();
        $stylesheet->loadXML($this->stylesheet);
        assertThat(
                $this->xslProcessor->applyStylesheet($stylesheet)
                        ->getStylesheets(),
                equals([$stylesheet])
        );
    }

    /**
     * @test
     */
    public function applyStylesheetFromFileStoresStylesheet()
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

    /**
     * @test
     */
    public function failingToImportStylesheetFromFileThrowsIOException()
    {
        vfsStream::setup();
        expect(function() {
                $this->xslProcessor->applyStylesheetFromFile(vfsStream::url('root/doesNotExist.xsl'));
        })->throws(XslProcessorException::class);
    }

    /**
     * @test
     */
    public function singleParameters()
    {
        $this->baseXsltProcessor->returns(['setParameter' => true]);
        $this->xslProcessor->withParameter('foo', 'bar', 'baz')
                ->withParameter('foo', 'foo', 'bar');
        verify($this->baseXsltProcessor, 'setParameter')
                ->receivedOn(1, 'foo', 'bar', 'baz');
        verify($this->baseXsltProcessor, 'setParameter')
                ->receivedOn(2, 'foo', 'foo', 'bar');
    }

    /**
     * @test
     */
    public function failingToAddSingleParametersThrowsXSLProcessorException()
    {
        $this->baseXsltProcessor->returns(['setParameter' => false]);
        expect(function() {
                $this->xslProcessor->withParameter('foo', 'bar', 'baz');
        })->throws(XslProcessorException::class);
    }

    /**
     * @test
     */
    public function arrayParameters()
    {
        $this->baseXsltProcessor->returns(['setParameter' => true]);
        $this->xslProcessor->withParameters('baz', ['baz' => 'bar'])
                ->withParameters('baz', ['foo' => 'bar']);
        verify($this->baseXsltProcessor, 'setParameter')
                ->receivedOn(1, 'baz', ['baz' => 'bar']);
        verify($this->baseXsltProcessor, 'setParameter')
                ->receivedOn(2, 'baz', ['foo' => 'bar']);
    }

    /**
     * @test
     */
    public function failingToAddListOfParametersThrowsXSLProcessorException()
    {
        $this->baseXsltProcessor->returns(['setParameter' => false]);
        expect(function() {
                $this->xslProcessor->withParameters('baz', ['bar' => 'baz']);
        })->throws(XslProcessorException::class);
    }

    /**
     * @test
     */
    public function cloneInstanceCopiesParameters()
    {
        $anotherBaseXsltProcessor = NewInstance::of('\XSLTProcessor');
        ExtendedXslProcessor::$xsltProcessor = $anotherBaseXsltProcessor;
        $this->xslProcessor->withParameter('foo', 'bar', 'baz');
        $this->baseXsltProcessor->returns(['importStylesheet' => true]);
        $anotherBaseXsltProcessor->returns(['importStylesheet' => true]);
        $this->xslProcessor->applyStylesheet(new \DOMDocument());
        $clonedXSLProcessor = clone $this->xslProcessor;
        verify($anotherBaseXsltProcessor, 'setParameter')
                ->received('foo', ['bar' => 'baz']);
    }

    /**
     * @test
     */
    public function cloneInstanceCopiesStylesheets()
    {
        $anotherBaseXsltProcessor = NewInstance::of('\XSLTProcessor');
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

    /**
     * @test
     */
    public function cloneInstanceDoesNotCopyDocumentToTransform()
    {
      ExtendedXslProcessor::$xsltProcessor = NewInstance::of('\XSLTProcessor')
                ->returns(['importStylesheet' => true]);
        $this->baseXsltProcessor->returns(['importStylesheet' => true]);
        $this->xslProcessor->applyStylesheet(new \DOMDocument());
        $clonedXSLProcessor = clone $this->xslProcessor;
        expect(function() use($clonedXSLProcessor) {
                $clonedXSLProcessor->toDoc();
        })->throws(XslProcessorException::class);
    }

    /**
     * @test
     */
    public function transformToDocWithoutDocThrowsXslProcessorException()
    {
        $this->xslProcessor = new XslProcessor(new XslCallbacks());
        expect(function() {
                $this->xslProcessor->toDoc();
        })->throws(XslProcessorException::class);
    }

    /**
     * @test
     */
    public function transformToDocReturnsDOMDocument()
    {
        $result = new \DOMDocument();
        $result->loadXML('<?xml version="1.0" encoding="UTF-8"?><foo><bar/></foo>');
        $this->baseXsltProcessor->returns(['transformToDoc' => $result]);
        assertThat($this->xslProcessor->toDoc(), isInstanceOf(\DOMDocument::class));
    }

    /**
     * @test
     */
    public function failingTransformationToDomDocumentThrowsXSLProcessorException()
    {
        $this->baseXsltProcessor->returns(['transformToDoc' => false]);
        expect(function() {
                $this->xslProcessor->toDoc();
        })->throws(XslProcessorException::class);
    }

    /**
     * @test
     */
    public function transformToUriWithoutDocThrowsXslProcessorException()
    {
        $this->xslProcessor = new XslProcessor(new XslCallbacks());
        expect(function() {
                $this->xslProcessor->toUri('foo');
        })->throws(XslProcessorException::class);
    }

    /**
     * test transforming a document
     *
     * @test
     */
    public function transformToUri()
    {
        $this->baseXsltProcessor->returns(['transformToUri' => 4555]);
        assertThat($this->xslProcessor->toUri('foo'), equals(4555));
    }

    /**
     * @test
     */
    public function failingTransformationToUriThrowsXSLProcessorException()
    {
        $this->baseXsltProcessor->returns(['transformToUri' => false]);
        expect(function() {
                $this->xslProcessor->toUri('foo');
        })->throws(XslProcessorException::class);
    }

    /**
     * @test
     */
    public function transformToXmlWithoutDocThrowsXslProcessorException()
    {
        $this->xslProcessor = new XslProcessor(new XslCallbacks());
        expect(function() {
                $this->xslProcessor->toXml();
        })->throws(XslProcessorException::class);
    }

    /**
     * test transforming a document
     *
     * @test
     */
    public function transformToXmlReturnsTransformedXml()
    {
        $this->baseXsltProcessor->returns(['transformToXml' => '<foo>']);
        assertThat($this->xslProcessor->toXML(), equals('<foo>'));
    }

    /**
     * @test
     */
    public function failingTransformationToXmlThrowsXSLProcessorException()
    {
        $this->baseXsltProcessor->returns(['transformToXml' => false]);
        expect(function() {
                $this->xslProcessor->toXml();
        })->throws(XslProcessorException::class);
    }

    /**
     * @test
     */
    public function tooLessParamsForCallbackInvocationThrowsCallbackException()
    {
        expect(function() { XslProcessor::invokeCallback(); })
                ->throws(XslCallbackException::class);
    }

    /**
     * @test
     */
    public function invokesCorrectCallback()
    {
        $callback     = new XslExampleCallback();
        $xslProcessor = new ExtendedXslProcessor(new XslCallbacks());
        $xslProcessor->usingCallback('foo', $callback)->callRegisterCallbacks();
        XslProcessor::invokeCallback('foo', 'youCanDoThis');
        assertTrue($callback->calledYouCanDoThis());
    }

    /**
     * @test
     */
    public function passesParametersToCallback()
    {
        $callback     = new XslExampleCallback();
        $xslProcessor = new ExtendedXslProcessor(new XslCallbacks());
        $xslProcessor->usingCallback('foo', $callback)->callRegisterCallbacks();
        XslProcessor::invokeCallback('foo', 'hello', 'mikey');
        assertThat($callback->getHelloArg(), equals('mikey'));
    }
}
