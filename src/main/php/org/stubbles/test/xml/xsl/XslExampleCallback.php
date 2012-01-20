<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  net\stubbles\xml
 */
namespace org\stubbles\test\xml\xsl;
use net\stubbles\lang\BaseObject;
/**
 * Class to register classes and make their methods available as callback in xsl.
 */
class XslExampleCallback extends BaseObject
{
    /**
     * the argument given to hello()
     *
     * @var  string
     */
    private $helloArg                  = null;
    /**
     * check whether method was called
     *
     * @type  bool
     */
    private static $calledYouCanDoThis = false;

    /**
     * example method
     *
     * @param   string  $world
     * @return  string
     * @XslMethod
     */
    public function hello($world)
    {
        $this->helloArg = $world;
        return 'hello ' . $world;
    }

    /**
     * returns last argument for hello()
     *
     * @return  string
     */
    public function getHelloArg()
    {
        return $this->helloArg;
    }

    /**
     * example method
     *
     * @return  string
     */
    public function youCanNotCallMe()
    {
        return 'bye world!';
    }

    /**
     * example method
     *
     * @return  string
     * @XslMethod
     */
    protected function doNotCallMe()
    {
        return 'A protected method was called!';
    }

    /**
     * example method
     *
     * @return  string
     * @XslMethod
     */
    private function doNotCallMeToo()
    {
        return 'A private method was called.';
    }

    /**
     * example method
     *
     * @return  string
     * @XslMethod
     */
    public static function youCanDoThis()
    {
        self::$calledYouCanDoThis = true;
        return 'A static method was called.';
    }

    /**
     * checks whether youCanCallMe() was called
     *
     * @return  bool
     */
    public static function calledYouCanDoThis()
    {
        return self::$calledYouCanDoThis;
    }
}
?>