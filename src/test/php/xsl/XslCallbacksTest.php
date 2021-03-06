<?php
declare(strict_types=1);
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace stubbles\xml\xsl;
use PHPUnit\Framework\TestCase;
use stubbles\helper\xsl\XslExampleCallback;

use function bovigo\assert\assertThat;
use function bovigo\assert\expect;
use function bovigo\assert\predicate\equals;
/**
 * Test for stubbles\xml\xsl\XslCallbacks.
 *
 * @group  xml
 * @group  xml_xsl
 */
class XslCallbacksTest extends TestCase
{
    /**
     * @var  XslExampleCallback
     */
    private $callback;
    /**
     * @var  XslCallbacks
     */
    private $xslCallbacks;

    protected function setUp(): void
    {
        $this->callback     = new XslExampleCallback();
        $this->xslCallbacks = new XslCallbacks();
        $this->xslCallbacks->addCallback('test', $this->callback);
    }

    /**
     * @test
     */
    public function returnsListOfCallbacks(): void
    {
        assertThat(
                $this->xslCallbacks->getCallbacks(),
                equals(['test' => $this->callback])
        );
    }

    /**
     * @test
     */
    public function callbackDoesNotExistThrowsCallbackException(): void
    {
        expect(function() { $this->xslCallbacks->invoke('foo', 'hello'); })
                ->throws(XslCallbackException::class);
    }

    /**
     * @test
     */
    public function callbackNonExistingMethodThrowsCallbackException(): void
    {
        expect(function() { $this->xslCallbacks->invoke('test', 'doesNotExist'); })
                ->throws(XslCallbackException::class);
    }

    /**
     * @test
     */
    public function callingNonAnnotatedMethodThrowsCallbackException(): void
    {
       expect(function() { $this->xslCallbacks->invoke('test', 'youCanNotCallMe'); })
               ->throws(XslCallbackException::class);
    }

    /**
     * @test
     */
    public function callingProtectedCallbackMethodThrowsCallbackException(): void
    {
        expect(function() { $this->xslCallbacks->invoke('test', 'doNotCallMe'); })
                ->throws(XslCallbackException::class);
    }

    /**
     * @test
     */
    public function callingPrivateCallbackMethodThrowsCallbackException(): void
    {
        expect(function() { $this->xslCallbacks->invoke('test', 'doNotCallMeToo'); })
                ->throws(XslCallbackException::class);
    }

    /**
     * @test
     */
    public function invokeReturnsValueFromCallbackMethod(): void
    {
        assertThat(
                $this->xslCallbacks->invoke('test', 'hello', ['world!']),
                equals('hello world!')
        );
    }

    /**
     * @test
     */
    public function invokeReturnsValueFromStaticCallbackMethod(): void
    {
        assertThat(
                $this->xslCallbacks->invoke('test', 'youCanDoThis'),
                equals('A static method was called.')
        );
    }
}
