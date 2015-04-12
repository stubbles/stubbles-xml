<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  stubbles\xml
 */
namespace stubbles\xml\xsl;
use bovigo\callmap;
use bovigo\callmap\NewInstance;
use org\bovigo\vfs\vfsStream;
require_once __DIR__ . '/XslExampleCallback.php';
use org\stubbles\test\xml\xsl\XslExampleCallback;
use stubbles\lang\reflect;
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
        $this->baseXsltProcessor = NewInstance::of('\XSLTProcessor');
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
        assertEquals(
                'stubbles\xml\xsl\XslProcessorProvider',
                reflect\annotationsOf(__NAMESPACE__ . '\XslProcessor')
                        ->firstNamed('ProvidedBy')
                        ->getProviderClass()
                        ->getName()
        );
    }

    /**
     * @test
     * @group  bug165
     */
    public function enableProfilingBySettingPathToProfileDataFile()
    {
        vfsStream::setup();
        assertSame(
                $this->xslProcessor,
                $this->xslProcessor->enableProfiling(vfsStream::url('root/profile.txt'))
        );
        callmap\verify($this->baseXsltProcessor, 'setProfiling')
                ->received(vfsStream::url('root/profile.txt'));
    }

    /**
     * @test
     */
    public function onDocumentReturnsItself()
    {
        assertSame(
                $this->xslProcessor,
                $this->xslProcessor->onDocument($this->document)
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
        assertSame(
                $this->xslProcessor,
                $this->xslProcessor->onXmlFile(vfsStream::url('root/test.xsl'))
        );
    }

    /**
     * @test
     * @expectedException  stubbles\lang\exception\IOException
     */
    public function onXMLFileThrowsIoExceptionIfFileDoesNotExist()
    {
        vfsStream::setup();
        $this->xslProcessor->onXmlFile(vfsStream::url('root/doesNotExist.xsl'));
    }

    /**
     * @test
     */
    public function applyStylesheetStoresStylesheet()
    {
        $stylesheet = new \DOMDocument();
        $stylesheet->loadXML($this->stylesheet);
        assertSame(
                $this->xslProcessor,
                $this->xslProcessor->applyStylesheet($stylesheet)
        );
        assertEquals([$stylesheet], $this->xslProcessor->getStylesheets());
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
        assertEquals(
                1,
                count(
                        $this->xslProcessor
                                ->applyStylesheetFromFile(vfsStream::url('root/test.xsl'))
                                ->getStylesheets()
                )
        );
    }

    /**
     * @test
     * @expectedException  stubbles\lang\exception\IOException
     */
    public function failingToImportStylesheetFromFileThrowsIOException()
    {
        vfsStream::setup();
        $this->xslProcessor->applyStylesheetFromFile(vfsStream::url('root/doesNotExist.xsl'));
    }

    /**
     * @test
     */
    public function singleParameters()
    {
        $this->baseXsltProcessor->mapCalls(['setParameter' => true]);
        $this->xslProcessor->withParameter('foo', 'bar', 'baz')
                ->withParameter('foo', 'foo', 'bar');
        callmap\verify($this->baseXsltProcessor, 'setParameter')
                ->receivedOn(1, 'foo', 'bar', 'baz');
        callmap\verify($this->baseXsltProcessor, 'setParameter')
                ->receivedOn(2, 'foo', 'foo', 'bar');
    }

    /**
     * @test
     * @expectedException  stubbles\xml\xsl\XslProcessorException
     */
    public function failingToAddSingleParametersThrowsXSLProcessorException()
    {
        $this->baseXsltProcessor->mapCalls(['setParameter' => false]);
        $this->xslProcessor->withParameter('foo', 'bar', 'baz');
    }

    /**
     * @test
     */
    public function arrayParameters()
    {
        $this->baseXsltProcessor->mapCalls(['setParameter' => true]);
        $this->xslProcessor->withParameters('baz', ['baz' => 'bar'])
                ->withParameters('baz', ['foo' => 'bar']);
        callmap\verify($this->baseXsltProcessor, 'setParameter')
                ->receivedOn(1, 'baz', ['baz' => 'bar']);
        callmap\verify($this->baseXsltProcessor, 'setParameter')
                ->receivedOn(2, 'baz', ['foo' => 'bar']);
    }

    /**
     * @test
     * @expectedException  stubbles\xml\xsl\XslProcessorException
     */
    public function failingToAddListOfParametersThrowsXSLProcessorException()
    {
        $this->baseXsltProcessor->mapCalls(['setParameter' => false]);
        $this->xslProcessor->withParameters('baz', ['bar' => 'baz']);
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
        callmap\verify($anotherBaseXsltProcessor, 'setParameter')
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
        callmap\verify($anotherBaseXsltProcessor, 'importStylesheet')
                ->received($stylesheet);
    }

    /**
     * @test
     * @expectedException  stubbles\xml\xsl\XslProcessorException
     */
    public function cloneInstanceDoesNotCopyDocumentToTransform()
    {
        TestXslProcessor::$xsltProcessor = NewInstance::of('\XSLTProcessor')
                ->mapCalls(['importStylesheet' => true]);
        $this->baseXsltProcessor->mapCalls(['importStylesheet' => true]);
        $this->xslProcessor->applyStylesheet(new \DOMDocument());
        $clonedXSLProcessor = clone $this->xslProcessor;
        $clonedXSLProcessor->toDoc();
    }

    /**
     * @test
     * @expectedException  stubbles\xml\xsl\XslProcessorException
     */
    public function transformToDocWithoutDocThrowsXslProcessorException()
    {
        $this->xslProcessor = new XslProcessor(new XslCallbacks());
        $this->xslProcessor->toDoc();
    }

    /**
     * @test
     */
    public function transformToDocReturnsDOMDocument()
    {
        $result = new \DOMDocument();
        $result->loadXML('<?xml version="1.0" encoding="UTF-8"?><foo><bar/></foo>');
        $this->baseXsltProcessor->mapCalls(['transformToDoc' => $result]);
        assertInstanceOf('\DOMDocument', $this->xslProcessor->toDoc());
    }

    /**
     * @test
     * @expectedException  stubbles\xml\xsl\XslProcessorException
     */
    public function failingTransformationToDomDocumentThrowsXSLProcessorException()
    {
        $this->baseXsltProcessor->mapCalls(['transformToDoc' => false]);
        $this->xslProcessor->toDoc();
    }

    /**
     * @test
     * @expectedException  stubbles\xml\xsl\XslProcessorException
     */
    public function transformToUriWithoutDocThrowsXslProcessorException()
    {
        $this->xslProcessor = new XslProcessor(new XslCallbacks());
        $this->xslProcessor->toUri('foo');
    }

    /**
     * test transforming a document
     *
     * @test
     */
    public function transformToUri()
    {
        $this->baseXsltProcessor->mapCalls(['transformToUri' => 4555]);
        assertEquals(4555, $this->xslProcessor->toUri('foo'));
    }

    /**
     * @test
     * @expectedException  stubbles\xml\xsl\XslProcessorException
     */
    public function failingTransformationToUriThrowsXSLProcessorException()
    {
        $this->baseXsltProcessor->mapCalls(['transformToUri' => false]);
        $this->xslProcessor->toUri('foo');
    }

    /**
     * @test
     * @expectedException  stubbles\xml\xsl\XslProcessorException
     */
    public function transformToXmlWithoutDocThrowsXslProcessorException()
    {
        $this->xslProcessor = new XslProcessor(new XslCallbacks());
        $this->xslProcessor->toXml();
    }

    /**
     * test transforming a document
     *
     * @test
     */
    public function transformToXmlReturnsTransformedXml()
    {
        $this->baseXsltProcessor->mapCalls(['transformToXml' => '<foo>']);
        assertEquals('<foo>', $this->xslProcessor->toXML());
    }

    /**
     * @test
     * @expectedException  stubbles\xml\xsl\XslProcessorException
     */
    public function failingTransformationToXmlThrowsXSLProcessorException()
    {
        $this->baseXsltProcessor->mapCalls(['transformToXml' => false]);
        $this->xslProcessor->toXml();
    }

    /**
     * @test
     * @expectedException  stubbles\xml\xsl\XslCallbackException
     */
    public function tooLessParamsForCallbackInvocationThrowsCallbackException()
    {
        XslProcessor::invokeCallback();
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
        assertEquals('mikey', $callback->getHelloArg());
    }
}
