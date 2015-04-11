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
require_once __DIR__ . '/XslExampleCallback.php';
use org\stubbles\test\xml\xsl\XslExampleCallback;
/**
 * Test for stubbles\xml\xsl\XslCallbacks.
 *
 * @group  xml
 * @group  xml_xsl
 */
class XslCallbacksTest extends \PHPUnit_Framework_TestCase
{
    /**
     * callback class used for tests
     *
     * @type  XslExampleCallback
     */
    private $callback;
    /**
     * instance to test
     *
     * @type  XslCallbacks
     */
    private $xslCallbacks;

    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->callback     = new XslExampleCallback();
        $this->xslCallbacks = new XslCallbacks();
        $this->xslCallbacks->addCallback('test', $this->callback);
    }

    /**
     * @test
     */
    public function returnsListOfCallbacks()
    {
        assertEquals(
                ['test' => $this->callback],
                $this->xslCallbacks->getCallbacks()
        );
    }

    /**
     * @test
     * @expectedException  stubbles\xml\xsl\XslCallbackException
     */
    public function callbackDoesNotExistThrowsCallbackException()
    {
        $this->xslCallbacks->invoke('foo', 'hello');
    }

    /**
     * @test
     * @expectedException  stubbles\xml\xsl\XslCallbackException
     */
    public function callbackNonExistingMethodThrowsCallbackException()
    {
        $this->xslCallbacks->invoke('test', 'doesNotExist');
    }

    /**
     * @test
     * @expectedException  stubbles\xml\xsl\XslCallbackException
     */
    public function callingNonAnnotatedMethodThrowsCallbackException()
    {
       $this->xslCallbacks->invoke('test', 'youCanNotCallMe');
    }

    /**
     * @test
     * @expectedException  stubbles\xml\xsl\XslCallbackException
     */
    public function callingProtectedCallbackMethodThrowsCallbackException()
    {
        $this->xslCallbacks->invoke('test', 'doNotCallMe');
    }

    /**
     * @test
     * @expectedException  stubbles\xml\xsl\XslCallbackException
     */
    public function callingPrivateCallbackMethodThrowsCallbackException()
    {
        $this->xslCallbacks->invoke('test', 'doNotCallMeToo');
    }

    /**
     * @test
     */
    public function invokeReturnsValueFromCallbackMethod()
    {
        assertEquals(
                'hello world!',
                $this->xslCallbacks->invoke('test', 'hello', ['world!'])
        );
    }

    /**
     * @test
     */
    public function invokeReturnsValueFromStaticCallbackMethod()
    {
        assertEquals(
                'A static method was called.',
                $this->xslCallbacks->invoke('test', 'youCanDoThis')
        );
    }
}
