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
use stubbles\ioc\InjectionProvider;
use stubbles\ioc\Injector;
use stubbles\lang\exception\ConfigurationException;
/**
 * Injection provider for XSL processor instances.
 *
 * @since  1.5.0
 */
class XslProcessorProvider implements InjectionProvider
{
    /**
     * injector instance to create instances of other classes
     *
     * @type  Injector
     */
    private $injector;
    /**
     * path to config files
     *
     * @type  string
     */
    private $configPath;
    /**
     * list of callbacks
     *
     * @type  array
     */
    private $callbackList;

    /**
     * constructor
     *
     * @param  Injector  $injector
     * @param  string    $configPath
     * @Inject
     * @Named{configPath}('stubbles.config.path')
     */
    public function  __construct(Injector $injector, $configPath)
    {
        $this->injector   = $injector;
        $this->configPath = $configPath;
    }

    /**
     * returns the value to provide
     *
     * The given name is interpreted in regard on whether the xsl processor
     * instance to create should have callbacks or not. If $name equals
     * <code>stubbles.xml.xsl.callbacks.disabled</code> the resulting
     * instance will not have callbacks.
     *
     * Callbacks are read from xsl-callbacks.ini within the given config path.
     *
     * @param   string  $name
     * @return  mixed
     */
    public function get($name = null)
    {
        if ($this->shouldHaveCallbacks($name)) {
            return new XslProcessor($this->createXslCallbacks());
        }

        return new XslProcessor(new XslCallbacks());
    }

    /**
     * checks whether the xsl processor instance to create should have callbacks
     *
     * @param   string  $name
     * @return  bool
     */
    protected function shouldHaveCallbacks($name)
    {
        return ('stubbles.xml.xsl.callbacks.disabled' !== $name);
    }

    /**
     * creates callbacks
     *
     * @return  XslCallbacks
     */
    protected function createXslCallbacks()
    {
        $callbacks = new XslCallbacks();
        foreach ($this->getCallbackList() as $callbackName => $callbackClass) {
            $callbacks->addCallback($callbackName, $this->injector->getInstance($callbackClass));
        }

        return $callbacks;
    }

    /**
     * reads list of callbacks from configuration
     *
     * @return  array
     * @throws  ConfigurationException
     */
    protected function getCallbackList()
    {
        if (!is_array($this->callbackList)) {
            if (!file_exists($this->configPath . '/xsl-callbacks.ini')) {
                $this->callbackList = [];
            } else {
                $this->callbackList = @parse_ini_file($this->configPath . '/xsl-callbacks.ini');
                if (false === $this->callbackList) {
                    throw new ConfigurationException('XSL callback in ' . $this->configPath . '/xsl-callbacks.ini contains errors and can not be parsed.');
                }
            }
        }

        return $this->callbackList;
    }
}
