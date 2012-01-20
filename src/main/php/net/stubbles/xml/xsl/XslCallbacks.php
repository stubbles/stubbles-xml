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
use net\stubbles\lang\Object;
/**
 * Class to register classes and make their methods available as callback in xsl.
 */
class XslCallbacks extends BaseObject
{
    /**
     * list of callback instances
     *
     * @type  Object[]
     */
    protected $callbacks = array();

    /**
     * register a new instance as callback
     *
     * @param  string  $name      name to register the callback under
     * @param  Object  $callback
     */
    public function addCallback($name, Object $callback)
    {
        $this->callbacks[$name] = $callback;
    }

    /**
     * returns list of added callbacks
     *
     * @return  Object[]
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
     * @return  Object
     * @throws  XslCallbackException
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
     */
    public function invoke($name, $methodName, array $arguments = array())
    {
        $callback   = $this->getCallback($name);
        $class      = $callback->getClass();
        if (!$class->hasMethod($methodName)) {
            throw new XslCallbackException('Callback with name ' . $name . ' does not have a method named ' . $methodName);
        }

        $method = $class->getMethod($methodName);
        if (!$method->hasAnnotation('XslMethod')) {
            throw new XslCallbackException('The callback\'s ' . $name . ' ' . $callback->getClassName() . '::' . $methodName . '() is not annotated as XslMethod.');
        }

        if (!$method->isPublic()) {
            throw new XslCallbackException('The callback\'s ' . $name . ' ' . $callback->getClassName() . '::' . $methodName . '() is not a public method.');
        }

        if ($method->isStatic()) {
            return $method->invokeArgs(null, $arguments);
        }

        return $method->invokeArgs($callback, $arguments);
    }
}
?>