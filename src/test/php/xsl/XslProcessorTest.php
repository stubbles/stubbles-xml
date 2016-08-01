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
namespace stubbles\xml\xsl;
use bovigo\callmap\NewInstance;
use org\bovigo\vfs\vfsStream;
require_once __DIR__ . '/XslExampleCallback.php';
use org\stubbles\test\xml\xsl\XslExampleCallback;

use function bovigo\assert\assert;
use function bovigo\assert\assertTrue;
use function bovigo\assert\expect;
use function bovigo\assert\predicate\equals;
use function bovigo\assert\predicate\isInstanceOf;
use function bovigo\assert\predicate\isOfSize;
use function bovigo\assert\predicate\isSameAs;
use function bovigo\callmap\verify;
use function stubbles\reflect\annotationsOf;
/**
 * Helper class for the test.
 */
class TestXslProcessor extends XslProcessor
{
    /**
     * mocked xslt processor
     *
     * @type  \bovigo\callmap\Proxy
     */
    public static $xsltProcessor;

    /**
     * overwrite creation method to inject the mock object
     */
    protected function createXsltProcessor()
    {
        return self::$xsltProcessor;
    }

    /**
     * makes sure callbacks are registered
     */
    public function registerCallbacks()
    {
        parent::registerCallbacks();
    }
}
/**
 * Test for stubbles\xml\xsl\XslProcessor.
 *
 * @group     xml
 * @group     xml_xsl
 * @requires  extension  xsl
 */
class XslProcessorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * instance to test
     *
     * @type  XslProcessor
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

    /**
     * set up test environment
     */
    public function setUp()
    {
        libxml_clear_errors();
        $this->baseXsltProcessor = NewInstance::of(\XSLTProcessor::class);
        TestXslProcessor::$xsltProcessor = $this->baseXsltProcessor;
        $this->xslProcessor = new TestXslProcessor(new XslCallbacks());
        $this->document     = new \DOMDocument();
        $this->document->loadXML('<?xml version="1.0" encoding="UTF-8"?><foo><bar/></foo>');
        $this->xslProcessor->onDocument($this->document);
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
    public function providedByXslProcessorProvider()
    {
        assert(
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
        assert(
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
        assert(
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
        assert(
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
        assert(
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
        assert(
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
        $this->baseXsltProcessor->mapCalls(['setParameter' => true]);
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
        $this->baseXsltProcessor->mapCalls(['setParameter' => false]);
        expect(function() {
                $this->xslProcessor->withParameter('foo', 'bar', 'baz');
        })->throws(XslProcessorException::class);
    }

    /**
     * @test
     */
    public function arrayParameters()
    {
        $this->baseXsltProcessor->mapCalls(['setParameter' => true]);
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
        $this->baseXsltProcessor->mapCalls(['setParameter' => false]);
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
        TestXslProcessor::$xsltProcessor = $anotherBaseXsltProcessor;
        $this->xslProcessor->withParameter('foo', 'bar', 'baz');
        $this->baseXsltProcessor->mapCalls(['importStylesheet' => true]);
        $anotherBaseXsltProcessor->mapCalls(['importStylesheet' => true]);
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
        TestXslProcessor::$xsltProcessor = $anotherBaseXsltProcessor;
        $stylesheet = new \DOMDocument();
        $stylesheet->loadXML($this->stylesheet);
        $this->baseXsltProcessor->mapCalls(['importStylesheet' => true]);
        $anotherBaseXsltProcessor->mapCalls(['importStylesheet' => true]);
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
        TestXslProcessor::$xsltProcessor = NewInstance::of('\XSLTProcessor')
                ->mapCalls(['importStylesheet' => true]);
        $this->baseXsltProcessor->mapCalls(['importStylesheet' => true]);
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
        $this->baseXsltProcessor->mapCalls(['transformToDoc' => $result]);
        assert($this->xslProcessor->toDoc(), isInstanceOf(\DOMDocument::class));
    }

    /**
     * @test
     */
    public function failingTransformationToDomDocumentThrowsXSLProcessorException()
    {
        $this->baseXsltProcessor->mapCalls(['transformToDoc' => false]);
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
        $this->baseXsltProcessor->mapCalls(['transformToUri' => 4555]);
        assert($this->xslProcessor->toUri('foo'), equals(4555));
    }

    /**
     * @test
     */
    public function failingTransformationToUriThrowsXSLProcessorException()
    {
        $this->baseXsltProcessor->mapCalls(['transformToUri' => false]);
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
        $this->baseXsltProcessor->mapCalls(['transformToXml' => '<foo>']);
        assert($this->xslProcessor->toXML(), equals('<foo>'));
    }

    /**
     * @test
     */
    public function failingTransformationToXmlThrowsXSLProcessorException()
    {
        $this->baseXsltProcessor->mapCalls(['transformToXml' => false]);
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
        $xslProcessor = new TestXslProcessor(new XslCallbacks());
        $xslProcessor->usingCallback('foo', $callback)
                     ->registerCallbacks();
        XslProcessor::invokeCallback('foo', 'youCanDoThis');
        assertTrue($callback->calledYouCanDoThis());
    }

    /**
     * @test
     */
    public function passesParametersToCallback()
    {
        $callback     = new XslExampleCallback();
        $xslProcessor = new TestXslProcessor(new XslCallbacks());
        $xslProcessor->usingCallback('foo', $callback)->registerCallbacks();
        XslProcessor::invokeCallback('foo', 'hello', 'mikey');
        assert($callback->getHelloArg(), equals('mikey'));
    }
}
