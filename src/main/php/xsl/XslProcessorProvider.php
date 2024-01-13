<?php
declare(strict_types=1);
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace stubbles\xml\xsl;
use stubbles\ioc\InjectionProvider;
use stubbles\ioc\Injector;
/**
 * Injection provider for XSL processor instances.
 *
 * @since  1.5.0
 * @implements  InjectionProvider<XslProcessor>
 */
class XslProcessorProvider implements InjectionProvider
{
    /**
     * injector instance to create instances of other classes
     *
     * @var  \stubbles\ioc\Injector
     */
    private $injector;
    /**
     * path to config files
     *
     * @var  string
     */
    private $configPath;
    /**
     * list of callbacks
     *
     * @var  array<string,class-string>
     */
    private $callbackList;

    /**
     * constructor
     *
     * @param  \stubbles\ioc\Injector  $injector
     * @param  string                  $configPath
     * @Named{configPath}('stubbles.config.path')
     */
    public function  __construct(Injector $injector, string $configPath)
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
     */
    public function get(string $name = null): XslProcessor
    {
        if ($this->shouldHaveCallbacks($name)) {
            return new XslProcessor($this->createXslCallbacks());
        }

        return new XslProcessor(new XslCallbacks());
    }

    /**
     * checks whether the xsl processor instance to create should have callbacks
     */
    protected function shouldHaveCallbacks(string $name = null): bool
    {
        return 'stubbles.xml.xsl.callbacks.disabled' !== $name;
    }

    /**
     * creates callbacks
     */
    protected function createXslCallbacks(): XslCallbacks
    {
        $callbacks = new XslCallbacks();
        foreach ($this->callbacks() as $callbackName => $callbackClass) {
            $callbacks->addCallback(
                    $callbackName,
                    $this->injector->getInstance($callbackClass)
            );
        }

        return $callbacks;
    }

    /**
     * reads list of callbacks from configuration
     *
     * @return  array<string,class-string>
     * @throws  XslCallbackException
     */
    protected function callbacks(): array
    {
        if (!is_array($this->callbackList)) {
            $this->callbackList = $this->createCallbackList();
        }

        return $this->callbackList;
    }

    /**
     * @return  array<string,class-string>
     */
    private function createCallbackList(): array
    {
        $callbackConfig = $this->configPath . '/xsl-callbacks.ini';
        if (!file_exists($callbackConfig)) {
            return [];
        }

        $callbackList = @parse_ini_file($callbackConfig);
        if (false === $callbackList) {
            throw new XslCallbackException(
                sprintf(
                    'XSL callback config in %s contains errors and can not be parsed.',
                    $callbackConfig
                )
            );
        }

        return $callbackList;
    }
}
