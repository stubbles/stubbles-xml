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
use stubbles\ioc\Injector;

use function bovigo\assert\assert;
use function bovigo\assert\assertEmptyArray;
use function bovigo\assert\assertTrue;
use function bovigo\assert\expect;
use function bovigo\assert\predicate\equals;
use function stubbles\reflect\annotationsOfConstructorParameter;
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
        $this->injector             = NewInstance::stub(Injector::class);
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
        $configPathParamAnnotations = annotationsOfConstructorParameter(
                'configPath',
                $this->xslProcessorProvider
        );
        assertTrue($configPathParamAnnotations->contain('Named'));
        assert(
                $configPathParamAnnotations->firstNamed('Named')->getName(),
                equals('stubbles.config.path')
        );
    }

    /**
     * @test
     */
    public function createXslProcessorWithoutCallbacks()
    {
        assertEmptyArray(
                $this->xslProcessorProvider->get('stubbles.xml.xsl.callbacks.disabled')
                        ->getCallbacks()
        );
    }

    /**
     * @test
     */
    public function createWithNonExistingCallbackConfigurationReturnsXslProcessorWithoutCallbacks()
    {
        assertEmptyArray($this->xslProcessorProvider->get()->getCallbacks());
    }

    /**
     * @test
     */
    public function createWithInvalidCallbackConfigurationThrowsConfigurationException()
    {
        vfsStream::newFile('xsl-callbacks.ini')
                 ->withContent('!')
                 ->at($this->root);
        expect(function() { $this->xslProcessorProvider->get(); })
                ->throws(XslCallbackException::class);
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
        assert(
            $this->xslProcessorProvider->get()->getCallbacks(),
            equals(['foo' => $callback])
        );
    }
}
