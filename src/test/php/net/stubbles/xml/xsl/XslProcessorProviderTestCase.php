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
/**
 * Test for net\stubbles\xml\xsl\XslProcessorProvider.
 *
 * @since  1.5.0
 * @group  xml
 * @group  xml_xsl
 */
class XslProcessorProviderTestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * instance to test
     *
     * @type  XslProcessorProvider.
     */
    protected $xslProcessorProvider;
    /**
     * mocked injector instance
     *
     * @type  \PHPUnit_Framework_MockObject_MockObject
     */
    protected $mockInjector;
    /**
     * config directory
     *
     * @type  vfsStreamDirectory
     */
    protected $root;

    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->root                 = vfsStream::setup();
        $this->mockInjector         = $this->getMock('net\\stubbles\\ioc\\Injector');
        $this->xslProcessorProvider = new XslProcessorProvider($this->mockInjector,
                                                               vfsStream::url('root')
                                      );
    }

    /**
     * @test
     */
    public function annotationsPresentOnConstructor()
    {
        $constructor = $this->xslProcessorProvider->getClass()->getConstructor();

        $this->assertTrue($constructor->hasAnnotation('Inject'));

        $refParams = $constructor->getParameters();
        $this->assertTrue($refParams[1]->hasAnnotation('Named'));
        $this->assertEquals('net.stubbles.config.path',
                            $refParams[1]->getAnnotation('Named')->getName()
        );
    }

    /**
     * @test
     */
    public function createXslProcessorWithoutCallbacks()
    {
        $this->assertEquals(array(),
                            $this->xslProcessorProvider->get('net.stubbles.xml.xsl.callbacks.disabled')
                                                       ->getCallbacks()
        );
    }

    /**
     * @test
     */
    public function createWithNonExistingCallbackConfigurationReturnsXslProcessorWithoutCallbacks()
    {
        $this->assertEquals(array(),
                            $this->xslProcessorProvider->get()
                                                       ->getCallbacks()
        );
    }

    /**
     * @test
     * @expectedException  net\stubbles\lang\exception\ConfigurationException
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
                 ->withContent('foo="org\\stubbles\\example\\xsl\\ExampleCallback"')
                 ->at($this->root);
        $mockCallback = $this->getMock('net\\stubbles\\lang\\Object');
        $this->mockInjector->expects($this->once())
                           ->method('getInstance')
                           ->with($this->equalTo('org\\stubbles\\example\\xsl\\ExampleCallback'))
                           ->will($this->returnValue($mockCallback));
        $this->assertEquals(array('foo' => $mockCallback),
                            $this->xslProcessorProvider->get()
                                                       ->getCallbacks()
        );
    }
}
?>