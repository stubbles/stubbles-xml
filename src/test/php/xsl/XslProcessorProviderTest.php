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
use bovigo\callmap\NewInstance;
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
     * @type  \bovigo\callmap\Proxy
     */
    private $injector;
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
        $this->injector             = NewInstance::stub('stubbles\ioc\Injector');
        $this->xslProcessorProvider = new XslProcessorProvider(
                $this->injector,
                vfsStream::url('root')
        );
    }

    /**
     * @test
     */
    public function annotationsPresentOnConstructor()
    {
        $configPathParamAnnotations = reflect\annotationsOfConstructorParameter(
                'configPath',
                $this->xslProcessorProvider
        );
        assertTrue($configPathParamAnnotations->contain('Named'));
        assertEquals(
                'stubbles.config.path',
                $configPathParamAnnotations->firstNamed('Named')->getName()
        );
    }

    /**
     * @test
     */
    public function createXslProcessorWithoutCallbacks()
    {
        assertEquals(
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
        assertEquals(
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
        $callback = new \stdClass();
        $this->injector->mapCalls(['getInstance' => $callback]);
        assertEquals(
            ['foo' => $callback],
            $this->xslProcessorProvider->get()->getCallbacks()
        );
    }
}
