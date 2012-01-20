<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  net\stubbles\xml
 */
namespace net\stubbles\xml\xsl;
use org\bovigo\vfs\vfsStream;
use org\stubbles\test\xml\xsl\XslExampleCallback;
/**
 * Helper class for the test.
 */
class TestXslProcessor extends XslProcessor
{
    /**
     * mocked xslt processor
     *
     * @type  \PHPUnit_Framework_MockObject_MockObject
     */
    public static $mockXsltProcessor;

    /**
     * overwrite creation method to inject the mock object
     */
    protected function createXsltProcessor()
    {
        return self::$mockXsltProcessor;
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
 * Test for net\stubbles\xml\xsl\XslProcessor.
 *
 * @group  xml
 * @group  xml_xsl
 */
class XslProcessorTestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * instance to test
     *
     * @type  XslProcessor
     */
    protected $xslProcessor;
    /**
     * a mock for the XSLTProcessor
     *
     * @type  \PHPUnit_Framework_MockObject_MockObject
     */
    protected $mockXSLTProcessor;
    /**
     * a dom document to test
     *
     * @type  \DOMDocument
     */
    protected $document;

    /**
     * set up test environment
     */
    public function setUp()
    {
        if (extension_loaded('xsl') === false) {
            $this->markTestSkipped('net\\stubbles\\xml\\xsl\\XslProcessor requires PHP-extension "xsl".');
        }

        libxml_clear_errors();
        $this->mockXSLTProcessor = $this->getMock('\XSLTProcessor');
        TestXslProcessor::$mockXsltProcessor = $this->mockXSLTProcessor;
        $this->xslProcessor = new TestXslProcessor(new XslCallbacks());
        $this->document     = new \DOMDocument();
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
        $class = XslProcessor::newInstance(new XslCallbacks())->getClass();
        $this->assertTrue($class->hasAnnotation('ProvidedBy'));
        $this->assertEquals('net\\stubbles\\xml\\xsl\\XslProcessorProvider',
                            $class->getAnnotation('ProvidedBy')
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
        $this->mockXSLTProcessor->expects($this->once())
                                ->method('setProfiling')
                                ->with($this->equalTo(vfsStream::url('root/profile.txt')));
        $this->assertSame($this->xslProcessor,
                          $this->xslProcessor->enableProfiling(vfsStream::url('root/profile.txt'))
        );
    }

    /**
     * @test
     */
    public function onDocumentReturnsItself()
    {
        $this->assertSame($this->xslProcessor, $this->xslProcessor->onDocument($this->document));
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
        $this->assertSame($this->xslProcessor,
                          $this->xslProcessor->onXmlFile(vfsStream::url('root/test.xsl'))
        );
    }

    /**
     * @test
     * @expectedException  net\stubbles\lang\exception\IOException
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
        $this->assertSame($this->xslProcessor, $this->xslProcessor->applyStylesheet($stylesheet));
        $this->assertEquals(array($stylesheet), $this->xslProcessor->getStylesheets());
    }

    /**
     * @test
     */
    public function applyStylesheetFromFileStoresStylesheet()
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
        $this->assertEquals(1,
                            count($this->xslProcessor->applyStylesheetFromFile(vfsStream::url('root/test.xsl'))
                                                     ->getStylesheets()
                            )
        );
    }

    /**
     * @test
     * @expectedException  net\stubbles\lang\exception\IOException
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
        $this->mockXSLTProcessor->expects($this->at(0))
                                ->method('setParameter')
                                ->with($this->equalTo('foo'), $this->equalTo('bar'), $this->equalTo('baz'))
                                ->will($this->returnValue(true));
        $this->mockXSLTProcessor->expects($this->at(1))
                                ->method('setParameter')
                                ->with($this->equalTo('foo'), $this->equalTo('foo'), $this->equalTo('bar'))
                                ->will($this->returnValue(true));
        $this->xslProcessor->withParameter('foo', 'bar', 'baz')
                           ->withParameter('foo', 'foo', 'bar');
    }

    /**
     * @test
     * @expectedException  net\stubbles\xml\xsl\XslProcessorException
     */
    public function failingToAddSingleParametersThrowsXSLProcessorException()
    {
        $this->mockXSLTProcessor->expects($this->once())
                                ->method('setParameter')
                                ->with($this->equalTo('foo'), $this->equalTo('bar'), $this->equalTo('baz'))
                                ->will($this->returnValue(false));
        $this->xslProcessor->withParameter('foo', 'bar', 'baz');
    }

    /**
     * @test
     */
    public function arrayParameters()
    {
        $this->mockXSLTProcessor->expects($this->at(0))
                                ->method('setParameter')
                                ->with($this->equalTo('baz'), $this->equalTo(array('baz' => 'bar')))
                                ->will($this->returnValue(true));
        $this->mockXSLTProcessor->expects($this->at(1))
                                ->method('setParameter')
                                ->with($this->equalTo('baz'), $this->equalTo(array('foo' => 'bar')))
                                ->will($this->returnValue(true));
        $this->xslProcessor->withParameters('baz', array('baz' => 'bar'))
                           ->withParameters('baz', array('foo' => 'bar'));
    }

    /**
     * @test
     * @expectedException  net\stubbles\xml\xsl\XslProcessorException
     */
    public function failingToAddListOfParametersThrowsXSLProcessorException()
    {
        $this->mockXSLTProcessor->expects($this->once())
                                ->method('setParameter')
                                ->with($this->equalTo('baz'), $this->equalTo(array('bar' => 'baz')))
                                ->will($this->returnValue(false));
        $this->xslProcessor->withParameters('baz', array('bar' => 'baz'));
    }

    /**
     * @test
     */
    public function cloneInstanceCopiesParameters()
    {
        $anotherMockXSLTProcessor            = $this->getMock('\XSLTProcessor');
        TestXslProcessor::$mockXsltProcessor = $anotherMockXSLTProcessor;
        $this->xslProcessor->withParameter('foo', 'bar', 'baz');
        $this->xslProcessor->applyStylesheet(new \DOMDocument());
        $this->mockXSLTProcessor->expects($this->never())->method('setParameter');
        $anotherMockXSLTProcessor->expects($this->once())
                                 ->method('setParameter')
                                 ->with($this->equalTo('foo'), $this->equalTo(array('bar' => 'baz')));
        $clonedXSLProcessor = clone $this->xslProcessor;
    }

    /**
     * @test
     */
    public function cloneInstanceCopiesStylesheets()
    {
        $anotherMockXSLTProcessor            = $this->getMock('\XSLTProcessor');
        TestXslProcessor::$mockXsltProcessor = $anotherMockXSLTProcessor;
        $stylesheet = new \DOMDocument();
        $this->xslProcessor->applyStylesheet($stylesheet);
        $this->mockXSLTProcessor->expects($this->never())->method('importStylesheet');
        $anotherMockXSLTProcessor->expects($this->once())
                                 ->method('importStylesheet')
                                 ->with($this->equalTo($stylesheet));
        $clonedXSLProcessor = clone $this->xslProcessor;
    }

    /**
     * @test
     * @expectedException  net\stubbles\xml\xsl\XslProcessorException
     */
    public function cloneInstanceDoesNotCopyDocumentToTransform()
    {
        TestXslProcessor::$mockXsltProcessor = $this->getMock('\XSLTProcessor');
        $this->xslProcessor->applyStylesheet(new \DOMDocument());
        $clonedXSLProcessor = clone $this->xslProcessor;
        $clonedXSLProcessor->toDoc();
    }

    /**
     * @test
     * @expectedException  net\stubbles\xml\xsl\XslProcessorException
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
        $this->mockXSLTProcessor->expects($this->once())
                                ->method('transformToDoc')
                                ->with($this->equalTo($this->document))
                                ->will($this->returnValue(new \DOMDocument()));
        $this->assertInstanceOf('DOMDocument', $this->xslProcessor->toDoc());
    }

    /**
     * @test
     * @expectedException  net\stubbles\xml\xsl\XslProcessorException
     */
    public function failingTransformationToDomDocumentThrowsXSLProcessorException()
    {
        $this->mockXSLTProcessor->expects($this->once())
                                ->method('transformToDoc')
                                ->will($this->returnValue(false));
        $this->xslProcessor->toDoc();
    }

    /**
     * @test
     * @expectedException  net\stubbles\xml\xsl\XslProcessorException
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
        $this->mockXSLTProcessor->expects($this->exactly(2))
                                ->method('transformToUri')
                                ->with($this->equalTo($this->document))
                                ->will($this->onConsecutiveCalls(4555, 0));
        $this->assertEquals(4555, $this->xslProcessor->toUri('foo'));
        $this->assertEquals(0, $this->xslProcessor->toUri('foo'));
    }

    /**
     * @test
     * @expectedException  net\stubbles\xml\xsl\XslProcessorException
     */
    public function failingTransformationToUriThrowsXSLProcessorException()
    {
        $this->mockXSLTProcessor->expects($this->once())
                                ->method('transformToUri')
                                ->with($this->equalTo($this->document))
                                ->will($this->returnValue(false));
        $this->xslProcessor->toUri('foo');
    }

    /**
     * @test
     * @expectedException  net\stubbles\xml\xsl\XslProcessorException
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
        $this->mockXSLTProcessor->expects($this->exactly(2))
                                ->method('transformToXml')
                                ->with($this->equalTo($this->document))
                                ->will($this->onConsecutiveCalls('<foo>', ''));
        $this->assertEquals('<foo>', $this->xslProcessor->toXML());
        $this->assertEquals('', $this->xslProcessor->toXML());
    }

    /**
     * @test
     * @expectedException  net\stubbles\xml\xsl\XslProcessorException
     */
    public function failingTransformationToXmlThrowsXSLProcessorException()
    {
        $this->mockXSLTProcessor->expects($this->any())
                                ->method('transformToXml')
                                ->with($this->equalTo($this->document))
                                ->will($this->returnValue(false));
        $this->xslProcessor->toXml();
    }

    /**
     * @test
     * @expectedException  net\stubbles\xml\xsl\XslCallbackException
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
        $this->assertTrue($callback->calledYouCanDoThis());
    }

    /**
     * @test
     */
    public function passesParametersToCallback()
    {
        $callback     = new XslExampleCallback();
        $xslProcessor = new TestXslProcessor(new XslCallbacks());
        $xslProcessor->usingCallback('foo', $callback)
                     ->registerCallbacks();
        XslProcessor::invokeCallback('foo', 'hello', 'mikey');
        $this->assertEquals('mikey', $callback->getHelloArg());
    }
}
?>