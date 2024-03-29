<?php
declare(strict_types=1);
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace stubbles\xml\xsl;
use function stubbles\reflect\reflect;
use function stubbles\reflect\annotationsOf;
/**
 * Class to register classes and make their methods available as callback in xsl.
 */
class XslCallbacks
{
    /**
     * list of callback instances
     *
     * @var  object[]
     */
    private array $callbacks = [];

    /**
     * register a new instance as callback
     */
    public function addCallback(string $name, object $callback): void
    {
        $this->callbacks[$name] = $callback;
    }

    /**
     * returns list of added callbacks
     *
     * @return  object[]
     */
    public function getCallbacks(): array
    {
        return $this->callbacks;
    }

    /**
     * checks if a specific callback is known
     *
     * @param   string  $name
     * @return  bool
     */
    private function hasCallback(string $name): bool
    {
        return isset($this->callbacks[$name]);
    }

    /**
     * returns a callback
     *
     * @param   string  $name
     * @return  object
     * @throws  \stubbles\xml\xsl\XslCallbackException
     */
    private function callback(string $name)
    {
        if (!$this->hasCallback($name)) {
            throw new XslCallbackException('A callback with the name ' . $name . ' does not exist.');
        }

        return $this->callbacks[$name];
    }

    /**
     * invoke a method on a callback class
     *
     * @param   string   $name        name of callback instance to call method on
     * @param   string   $methodName  name of method to call
     * @param   mixed[]  $arguments   list of arguments for method to call
     * @return  mixed
     * @throws  \stubbles\xml\xsl\XslCallbackException
     */
    public function invoke(string $name, string $methodName, array $arguments = [])
    {
        $callback = $this->callback($name);
        if (!method_exists($callback, $methodName)) {
            throw new XslCallbackException('Callback with name ' . $name . ' does not have a method named ' . $methodName);
        }

        if (!annotationsOf($callback, $methodName)->contain('XslMethod')) {
            throw new XslCallbackException('The callback\'s ' . $name . ' ' . get_class($callback) . '::' . $methodName . '() is not annotated as XslMethod.');
        }

        $method = reflect($callback, $methodName);
        if (!($method instanceof \ReflectionMethod)) {
          throw new XslCallbackException('The callback\'s ' . $name . ' ' . get_class($callback) . '::' . $methodName . '() is not a method of a class.');
        }

        if (!$method->isPublic()) {
            throw new XslCallbackException('The callback\'s ' . $name . ' ' . get_class($callback) . '::' . $methodName . '() is not a public method.');
        }

        if ($method->isStatic()) {
            return $method->invokeArgs(null, $arguments);
        }

        return $method->invokeArgs($callback, $arguments);
    }
}
