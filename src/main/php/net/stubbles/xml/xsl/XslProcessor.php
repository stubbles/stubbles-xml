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
use net\stubbles\lang\Clonable;
use net\stubbles\lang\exception\IOException;
use net\stubbles\lang\exception\RuntimeException;
/**
 * Class to transform xml via xsl.
 *
 * @ProvidedBy(net\stubbles\xml\xsl\XslProcessorProvider.class)
 */
class XslProcessor implements Clonable
{
    /**
     * the document to transform
     *
     * @type  \DOMDocument
     */
    private $document;
    /**
     * the real processor used for the transformation
     *
     * @type  \XSLTProcessor
     */
    private $xsltProcessor;
    /**
     * list of parameters that were set
     *
     * @type  array
     */
    private $parameters       = array();
    /**
     * list of callbacks which should be available while processing the stylesheet
     *
     * @type  XslCallbacks
     */
    private $xslCallbacks;
    /**
     * workaround for limitation of XSLTProcessor::registerPHPFunctions()
     *
     * @type  XslCallback
     */
    private static $_callbacks;
    /**
     * list of used stylesheets
     *
     * @type  \DOMDocument[]
     */
    private $stylesheets      = array();

    /**
     * constructor
     *
     * @param   XslCallbacks  $callbacks
     * @throws  RuntimeException
     */
    public function __construct(XslCallbacks $callbacks)
    {
        if (!extension_loaded('xsl')) {
            throw new RuntimeException('Can not create ' . __CLASS__ . ', requires PHP-extension "xsl".');
        }

        $this->xslCallbacks  = $callbacks;
        $this->xsltProcessor = $this->createXsltProcessor();
    }

    /**
     * creates the XSLTProcessor instance
     *
     * @return  \XSLTProcessor
     */
    protected function createXsltProcessor()
    {
        return new \XSLTProcessor();
    }

    /**
     * does some corrections after cloning
     */
    public function __clone()
    {
        $this->xsltProcessor = $this->createXsltProcessor();
        foreach ($this->parameters as $nameSpace => $params) {
            $this->xsltProcessor->setParameter($nameSpace, $params);
        }

        foreach ($this->stylesheets as $stylesheet) {
            $this->xsltProcessor->importStylesheet($stylesheet);
        }

        $this->document = null;
    }

    /**
     * enables profiling of a transformation
     *
     * @param   string  $profileOutputFile  file to write profile data to
     * @return  XslProcessor
     * @since   2.0.0
     */
    public function enableProfiling($profileOutputFile)
    {
        $this->xsltProcessor->setProfiling($profileOutputFile);
        return $this;
    }

    /**
     * sets the document to transform
     *
     * @param   \DOMDocument  $doc
     * @return  XslProcessor
     */
    public function onDocument(\DOMDocument $doc)
    {
        $this->document = $doc;
        return $this;
    }

    /**
     * sets the document to transform
     *
     * @param   string  $xmlFile   name of the xml file containing the document to transform
     * @param   bool    $xinclude  whether to resolve xincludes or not, defaults to true
     * @return  XslProcessor
     * @throws  IOException
     */
    public function onXmlFile($xmlFile, $xinclude = true)
    {
        $doc = new \DOMDocument();
        if (false === @$doc->load($xmlFile)) {
            throw new IOException('Can not read xml document file ' . $xmlFile);
        }

        if (true === $xinclude) {
            $doc->xinclude();
        }

        return $this->onDocument($doc);
    }

    /**
     * add a stylesheet to use
     *
     * @param   \DOMDocument  $stylesheet
     * @return  XslProcessor
     */
    public function applyStylesheet(\DOMDocument $stylesheet)
    {
        $this->stylesheets[] = $stylesheet;
        $this->xsltProcessor->importStylesheet($stylesheet);
        return $this;
    }

    /**
     * add a stylesheet to use from a file
     *
     * @param   string  $stylesheetFile
     * @return  XslProcessor
     * @throws  IOException
     */
    public function applyStylesheetFromFile($stylesheetFile)
    {
        $stylesheet = new \DOMDocument();
        if (false === @$stylesheet->load($stylesheetFile)) {
            throw new IOException('Can not read stylesheet file ' . $stylesheetFile);
        }

        return $this->applyStylesheet($stylesheet);
    }

    /**
     * returns the list of used stylesheets
     *
     * @return  \DOMDocument[]
     */
    public function getStylesheets()
    {
        return $this->stylesheets;
    }

    /**
     * register an instance as callback
     *
     * @param   string  $name      name to register the callback under
     * @param   object  $instance  the instance to register as callback
     * @return  XslProcessor
     */
    public function usingCallback($name, $instance)
    {
        $this->xslCallbacks->addCallback($name, $instance);
        return $this;
    }

    /**
     * returns list of registered callbacks
     *
     * @return  object[]
     */
    public function getCallbacks()
    {
        return $this->xslCallbacks->getCallbacks();
    }

    /**
     * register all callback instances
     *
     * Workaround for limitation of XSLTProcessor::registerPHPFunctions()
     * callback instance in static variable to have data available when
     * php:function callback calls the static method as
     * XSLTProcessor::registerPHPFunctions() does not support non-static
     * methods nor anonymous functions directly.
     */
    protected function registerCallbacks()
    {
        self::$_callbacks = $this->xslCallbacks;
        $this->xsltProcessor->registerPHPFunctions(get_class($this) . '::invokeCallback');
    }

    /**
     * invoke a method on a callback class
     *
     * @return  mixed
     * @throws  XslCallbackException
     * @since   1.5.0
     */
    public static function invokeCallback()
    {
        $arguments = func_get_args();
        if (count($arguments) < 2) {
            throw new XslCallbackException('To less arguments: need at last two arguments to use callbacks.');
        }

        $name       = array_shift($arguments);
        $methodName = array_shift($arguments);
        return self::$_callbacks->invoke($name, $methodName, $arguments);
    }

    /**
     * sets a parameter for a namespace
     *
     * @param   string  $nameSpace   the namespace where the parameter is in
     * @param   string  $paramName   the name of the parameter to set
     * @param   string  $paramValue  the value to set the parameter to
     * @return  XslProcessor
     * @throws  XslProcessorException
     */
    public function withParameter($nameSpace, $paramName, $paramValue)
    {
        if (false === $this->xsltProcessor->setParameter($nameSpace, $paramName, $paramValue)) {
            throw new XslProcessorException('Could not set parameter ' . $nameSpace . ':' . $paramName . ' with value ' . $paramValue);
        }

        if (!isset($this->parameters[$nameSpace])) {
            $this->parameters[$nameSpace] = array();
        }

        $this->parameters[$nameSpace][$paramName] = $paramValue;
        return $this;
    }

    /**
     * set a list of parameters for the given namespace
     *
     * @param   string  $nameSpace  the namespace where the parameters are in
     * @param   array   $params     the list of parameters to set: name => value
     * @return  XslProcessor
     * @throws  XslProcessorException
     */
    public function withParameters($nameSpace, array $params)
    {
        if (false === $this->xsltProcessor->setParameter($nameSpace, $params)) {
            throw new XslProcessorException('Could not set parameters in ' . $nameSpace);
        }

        if (!isset($this->parameters[$nameSpace])) {
            $this->parameters[$nameSpace] = array();
        }

        $this->parameters[$nameSpace] = array_merge($this->parameters[$nameSpace], $params);
        return $this;
    }

    /**
     * transoforms the document into another DOMDocument
     *
     * @return  \DOMDocument
     * @throws  XslProcessorException
     */
    public function toDoc()
    {
        if (null === $this->document) {
            throw new XslProcessorException('Can not transform, set document or xml file to transform first');
        }

        $this->registerCallbacks();
        $result = $this->xsltProcessor->transformToDoc($this->document);
        if (false === $result) {
            throw new XslProcessorException($this->createMessage());
        }

        return $result;
    }

    /**
     * transforms the document and saves it to the given uri, returns the
     * amount of bytes written
     *
     * @param   string  $uri
     * @return  int
     * @throws  XslProcessorException
     */
    public function toUri($uri)
    {
        if (null === $this->document) {
            throw new XslProcessorException('Can not transform, set document or xml file to transform first');
        }

        $this->registerCallbacks();
        $bytes = $this->xsltProcessor->transformToURI($this->document, $uri);
        if (false === $bytes) {
            throw new XslProcessorException($this->createMessage());
        }

        return $bytes;
    }

    /**
     * transforms the document and returns the result as string
     *
     * @return  string
     * @throws  XslProcessorException
     */
    public function toXml()
    {
        if (null === $this->document) {
            throw new XslProcessorException('Can not transform, set document or xml file to transform first');
        }

        $this->registerCallbacks();
        $result = $this->xsltProcessor->transformToXML($this->document);
        if (false === $result) {
            throw new XslProcessorException($this->createMessage());
        }

        return $result;
    }

    /**
     * creates a message frim the last libxml error
     *
     * @return  string
     */
    private function createMessage()
    {
        $message = '';
        foreach (libxml_get_errors() as $error) {
            $message .= trim($error->message) . (($error->file) ? (' in file ' . $error->file) : ('')) . ' on line ' . $error->line . ' in column ' . $error->column . "\n";
        }

        libxml_clear_errors();
        if (strlen($message) === 0) {
            return 'Transformation failed: unknown error.';
        }

        return $message;
    }
}
?>