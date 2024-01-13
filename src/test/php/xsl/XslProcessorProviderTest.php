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
use PHPUnit\Framework\Attributes\RequiresPhpExtension;
use stubbles\ioc\Injector;

use function bovigo\assert\assertThat;
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
#[RequiresPhpExtension('xsl')]
class XslProcessorProviderTest extends TestCase
{
    /**
     * @var  XslProcessorProvider.
     */
    private $xslProcessorProvider;
    /**
     * @var  Injector&\bovigo\callmap\ClassProxy
     */
    private $injector;
    /**
     * config directory
     *
     * @var  \org\bovigo\vfs\vfsStreamDirectory
     */
    private $root;

    protected function setUp(): void
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
    public function annotationsPresentOnConstructor(): void
    {
        $configPathParamAnnotations = annotationsOfConstructorParameter(
                'configPath',
                $this->xslProcessorProvider
        );
        assertTrue($configPathParamAnnotations->contain('Named'));
        assertThat(
                $configPathParamAnnotations->firstNamed('Named')->getName(),
                equals('stubbles.config.path')
        );
    }

    /**
     * @test
     */
    public function createXslProcessorWithoutCallbacks(): void
    {
        assertEmptyArray(
                $this->xslProcessorProvider->get('stubbles.xml.xsl.callbacks.disabled')
                        ->getCallbacks()
        );
    }

    /**
     * @test
     */
    public function createWithNonExistingCallbackConfigurationReturnsXslProcessorWithoutCallbacks(): void
    {
        assertEmptyArray($this->xslProcessorProvider->get()->getCallbacks());
    }

    /**
     * @test
     */
    public function createWithInvalidCallbackConfigurationThrowsConfigurationException(): void
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
    public function createWithCallbacksReturnsXslProcessorWithCallbacks(): void
    {
        vfsStream::newFile('xsl-callbacks.ini')
                 ->withContent('foo="org\stubbles\example\xsl\ExampleCallback"')
                 ->at($this->root);
        $callback = new \stdClass();
        $this->injector->returns(['getInstance' => $callback]);
        assertThat(
            $this->xslProcessorProvider->get()->getCallbacks(),
            equals(['foo' => $callback])
        );
    }
}
