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
use PHPUnit\Framework\TestCase;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\RequiresPhpExtension;
use PHPUnit\Framework\Attributes\Test;
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
 */
#[Group('xml')]
#[Group('xml_xsl')]
#[RequiresPhpExtension('xsl')]
class XslProcessorProviderTest extends TestCase
{
    private XslProcessorProvider $xslProcessorProvider;
    private Injector&ClassProxy $injector;
    private vfsStreamDirectory $root;

    protected function setUp(): void
    {
        $this->root                 = vfsStream::setup();
        $this->injector             = NewInstance::stub(Injector::class);
        $this->xslProcessorProvider = new XslProcessorProvider(
            $this->injector,
            vfsStream::url('root')
        );
    }

    #[Test]
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

    #[Test]
    public function createXslProcessorWithoutCallbacks(): void
    {
        assertEmptyArray(
            $this->xslProcessorProvider->get('stubbles.xml.xsl.callbacks.disabled')
                ->getCallbacks()
        );
    }

    #[Test]
    public function createWithNonExistingCallbackConfigurationReturnsXslProcessorWithoutCallbacks(): void
    {
        assertEmptyArray($this->xslProcessorProvider->get()->getCallbacks());
    }

    #[Test]
    public function createWithInvalidCallbackConfigurationThrowsConfigurationException(): void
    {
        vfsStream::newFile('xsl-callbacks.ini')
            ->withContent('!')
            ->at($this->root);
        expect(function() { $this->xslProcessorProvider->get(); })
            ->throws(XslCallbackException::class);
    }

    #[Test]
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
