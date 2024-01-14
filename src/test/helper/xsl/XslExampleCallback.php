<?php
declare(strict_types=1);
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace stubbles\helper\xsl;
/**
 * Class to register classes and make their methods available as callback in xsl.
 */
class XslExampleCallback
{
    /**
     * the argument given to hello()
     */
    private ?string $helloArg = null;
    /**
     * check whether method was called
     */
    private static bool $calledYouCanDoThis = false;

    /**
     * example method
     *
     * @XslMethod
     */
    public function hello(string $world): string
    {
        $this->helloArg = $world;
        return 'hello ' . $world;
    }

    /**
     * returns last argument for hello()
     */
    public function getHelloArg(): ?string
    {
        return $this->helloArg;
    }

    /**
     * example method
     */
    public function youCanNotCallMe(): string
    {
        return 'bye world!';
    }

    /**
     * example method
     *
     * @XslMethod
     */
    protected function doNotCallMe(): string
    {
        return 'A protected method was called!';
    }

    /**
     * example method
     *
     * @XslMethod
     */
    private function doNotCallMeToo(): string
    {
        return 'A private method was called.';
    }

    /**
     * example method
     *
     * @XslMethod
     */
    public static function youCanDoThis(): string
    {
        self::$calledYouCanDoThis = true;
        return 'A static method was called.';
    }

    /**
     * checks whether youCanCallMe() was called
     */
    public static function calledYouCanDoThis(): bool
    {
        return self::$calledYouCanDoThis;
    }
}
?>
