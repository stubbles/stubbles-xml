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
use stubbles\lang;
use stubbles\lang\exception\IllegalArgumentException;
/**
 * Class to register classes and make their methods available as callback in xsl.
 */
class XslCallbacks
{
    /**
     * list of callback instances
     *
     * @type  Object[]
     */
    private $callbacks = [];

    /**
     * register a new instance as callback
     *
     * @param   string  $name      name to register the callback under
     * @param   object  $callback
     * @throws  \stubbles\lang\exception\IllegalArgumentException
     */
    public function addCallback($name, $callback)
    {
        if (!is_object($callback)) {
            throw new IllegalArgumentException('Given callback must be an object');
        }

        $this->callbacks[$name] = $callback;
    }

    /**
     * returns list of added callbacks
     *
     * @return  object[]
     */
    public function getCallbacks()
    {
        return $this->callbacks;
    }

    /**
     * checks if a specific callback is known
     *
     * @param   string  $name
     * @return  bool
     */
    private function hasCallback($name)
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
    private function getCallback($name)
    {
        if (!$this->hasCallback($name)) {
            throw new XslCallbackException('A callback with the name ' . $name . ' does not exist.');
        }

        return $this->callbacks[$name];
    }

    /**
     * invoke a method on a callback class
     *
     * @param   string  $name        name of callback instance to call method on
     * @param   string  $methodName  name of method to call
     * @param   array   $arguments   list of arguments for method to call
     * @return  mixed
     * @throws  \stubbles\xml\xsl\XslCallbackException
     */
    public function invoke($name, $methodName, array $arguments = [])
    {
        $callback = $this->getCallback($name);
        if (!method_exists($callback, $methodName)) {
            throw new XslCallbackException('Callback with name ' . $name . ' does not have a method named ' . $methodName);
        }

        $method = lang\reflect($callback, $methodName);
        if (!$method->hasAnnotation('XslMethod')) {
            throw new XslCallbackException('The callback\'s ' . $name . ' ' . get_class($callback) . '::' . $methodName . '() is not annotated as XslMethod.');
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
