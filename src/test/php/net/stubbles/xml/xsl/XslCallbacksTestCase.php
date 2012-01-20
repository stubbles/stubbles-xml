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
use net\stubbles\lang\BaseObject;
use org\stubbles\test\xml\xsl\XslExampleCallback;
/**
 * Test for net\stubbles\xml\xsl\XslCallbacks.
 *
 * @group  xml
 * @group  xml_xsl
 */
class XslCallbacksTestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * callback class used for tests
     *
     * @type  XslExampleCallback
     */
    protected $callback;
    /**
     * instance to test
     *
     * @type  XslCallbacks
     */
    protected $xslCallbacks;

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
     * @expectedException  net\stubbles\xml\xsl\XslCallbackException
     */
    public function callbackDoesNotExistThrowsCallbackException()
    {
        $this->xslCallbacks->invoke('foo', 'hello');
    }

    /**
     * @test
     * @expectedException  net\stubbles\xml\xsl\XslCallbackException
     */
    public function callingNonAnnotatedMethodThrowsCallbackException()
    {
       $this->xslCallbacks->invoke('test', 'youCanNotCallMe');
    }

    /**
     * @test
     * @expectedException  net\stubbles\xml\xsl\XslCallbackException
     */
    public function callingProtectedCallbackMethodThrowsCallbackException()
    {
        $this->xslCallbacks->invoke('test', 'doNotCallMe');
    }

    /**
     * @test
     * @expectedException  net\stubbles\xml\xsl\XslCallbackException
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
        $this->assertEquals('hello world!',
                            $this->xslCallbacks->invoke('test', 'hello', array('world!'))
        );
    }

    /**
     * @test
     */
    public function invokeReturnsValueFromStaticCallbackMethod()
    {
        $this->assertEquals('A static method was called.',
                            $this->xslCallbacks->invoke('test', 'youCanDoThis')
        );
    }
}
?>