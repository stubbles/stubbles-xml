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
use org\bovigo\vfs\vfsStream;
use stubbles\lang\reflect;
/**
 * Test for stubbles\xml\xsl\XslProcessorProvider.
 *
 * @since  1.5.0
 * @group  xml
 * @group  xml_xsl
 */
class XslProcessorProviderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * instance to test
     *
     * @type  XslProcessorProvider.
     */
    private $xslProcessorProvider;
    /**
     * mocked injector instance
     *
     * @type  \PHPUnit_Framework_MockObject_MockObject
     */
    private $mockInjector;
    /**
     * config directory
     *
     * @type  vfsStreamDirectory
     */
    private $root;

    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->root                 = vfsStream::setup();
        $this->mockInjector         = $this->getMockBuilder('stubbles\ioc\Injector')
                                           ->disableOriginalConstructor()
                                           ->getMock();
        $this->xslProcessorProvider = new XslProcessorProvider(
                $this->mockInjector,
                vfsStream::url('root')
        );
    }

    /**
     * @test
     */
    public function annotationsPresentOnConstructor()
    {
        $this->assertTrue(
                reflect\annotationsOfConstructor($this->xslProcessorProvider)
                        ->contain('Inject')
        );

        $configPathParamAnnotations = reflect\annotationsOfConstructorParameter(
                'configPath',
                $this->xslProcessorProvider
        );
        $this->assertTrue($configPathParamAnnotations->contain('Named'));
        $this->assertEquals(
                'stubbles.config.path',
                $configPathParamAnnotations->firstNamed('Named')->getName()
        );
    }

    /**
     * @test
     */
    public function createXslProcessorWithoutCallbacks()
    {
        $this->assertEquals(
                [],
                $this->xslProcessorProvider->get('stubbles.xml.xsl.callbacks.disabled')
                                           ->getCallbacks()
        );
    }

    /**
     * @test
     */
    public function createWithNonExistingCallbackConfigurationReturnsXslProcessorWithoutCallbacks()
    {
        $this->assertEquals(
                [],
                $this->xslProcessorProvider->get()->getCallbacks()
        );
    }

    /**
     * @test
     * @expectedException  stubbles\lang\exception\ConfigurationException
     */
    public function createWithInvalidCallbackConfigurationThrowsConfigurationException()
    {
        vfsStream::newFile('xsl-callbacks.ini')
                 ->withContent('!')
                 ->at($this->root);
        $this->xslProcessorProvider->get();
    }

    /**
     * @test
     */
    public function createWithCallbacksReturnsXslProcessorWithCallbacks()
    {
        vfsStream::newFile('xsl-callbacks.ini')
                 ->withContent('foo="org\stubbles\example\xsl\ExampleCallback"')
                 ->at($this->root);
        $mockCallback = $this->getMock('\stubbles\\lang\\Object');
        $this->mockInjector->expects($this->once())
                           ->method('getInstance')
                           ->with($this->equalTo('org\stubbles\example\xsl\ExampleCallback'))
                           ->will($this->returnValue($mockCallback));
        $this->assertEquals(
            ['foo' => $mockCallback],
            $this->xslProcessorProvider->get()->getCallbacks()
        );
    }
}
