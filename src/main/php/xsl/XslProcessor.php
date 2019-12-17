<?php
declare(strict_types=1);
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace stubbles\xml\xsl;
/**
 * Class to transform xml via xsl.
 *
 * @ProvidedBy(stubbles\xml\xsl\XslProcessorProvider.class)
 */
class XslProcessor
{
    /**
     * the document to transform
     *
     * @var  \DOMDocument|null
     */
    private $document;
    /**
     * the real processor used for the transformation
     *
     * @var  \XSLTProcessor|null
     */
    private $xsltProcessor;
    /**
     * list of parameters that were set
     *
     * @var  array<string,array<string,string>>
     */
    private $parameters       = [];
    /**
     * list of callbacks which should be available while processing the stylesheet
     *
     * @var  \stubbles\xml\xsl\XslCallbacks
     */
    private $xslCallbacks;
    /**
     * workaround for limitation of XSLTProcessor::registerPHPFunctions()
     *
     * @var  \stubbles\xml\xsl\XslCallbacks
     */
    private static $_callbacks;
    /**
     * list of used stylesheets
     *
     * @var  \DOMDocument[]
     */
    private $stylesheets      = [];

    /**
     * constructor
     *
     * @param   \stubbles\xml\xsl\XslCallbacks  $callbacks
     * @throws  \RuntimeException
     */
    public function __construct(XslCallbacks $callbacks)
    {
        if (!extension_loaded('xsl')) {
            throw new \RuntimeException(
                    'Can not create ' . __CLASS__
                    . ', requires PHP-extension "xsl".'
            );
        }

        $this->xslCallbacks  = $callbacks;
        $this->xsltProcessor = $this->createXsltProcessor();
    }

    /**
     * creates the XSLTProcessor instance
     *
     * @return  \XSLTProcessor
     */
    protected function createXsltProcessor(): \XSLTProcessor
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
     * @return  \stubbles\xml\xsl\XslProcessor
     * @since   2.0.0
     */
    public function enableProfiling(string $profileOutputFile): self
    {
        $this->xsltProcessor->setProfiling($profileOutputFile);
        return $this;
    }

    /**
     * sets the document to transform
     *
     * @param   \DOMDocument  $doc
     * @return  \stubbles\xml\xsl\XslProcessor
     */
    public function onDocument(\DOMDocument $doc): self
    {
        $this->document = $doc;
        return $this;
    }

    /**
     * sets the document to transform
     *
     * @param   string  $xmlFile   name of the xml file containing the document to transform
     * @param   bool    $xinclude  whether to resolve xincludes or not, defaults to true
     * @return  \stubbles\xml\xsl\XslProcessor
     * @throws  \stubbles\xml\xsl\XslProcessorException
     */
    public function onXmlFile(string $xmlFile, bool $xinclude = true): self
    {
        $doc = new \DOMDocument();
        if (false === @$doc->load($xmlFile)) {
            throw new XslProcessorException(
                    'Can not read xml document file ' . $xmlFile
            );
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
     * @return  \stubbles\xml\xsl\XslProcessor
     */
    public function applyStylesheet(\DOMDocument $stylesheet): self
    {
        $this->stylesheets[] = $stylesheet;
        $this->xsltProcessor->importStylesheet($stylesheet);
        return $this;
    }

    /**
     * add a stylesheet to use from a file
     *
     * @param   string  $stylesheetFile
     * @return  \stubbles\xml\xsl\XslProcessor
     * @throws  \stubbles\xml\xsl\XslProcessorException
     */
    public function applyStylesheetFromFile(string $stylesheetFile): self
    {
        $stylesheet = new \DOMDocument();
        if (false === @$stylesheet->load($stylesheetFile)) {
            throw new XslProcessorException('Can not read stylesheet file ' . $stylesheetFile);
        }

        return $this->applyStylesheet($stylesheet);
    }

    /**
     * returns the list of used stylesheets
     *
     * @return  \DOMDocument[]
     */
    public function getStylesheets(): array
    {
        return $this->stylesheets;
    }

    /**
     * register an instance as callback
     *
     * @param   string  $name      name to register the callback under
     * @param   object  $instance  the instance to register as callback
     * @return  \stubbles\xml\xsl\XslProcessor
     */
    public function usingCallback(string $name, $instance): self
    {
        $this->xslCallbacks->addCallback($name, $instance);
        return $this;
    }

    /**
     * returns list of registered callbacks
     *
     * @return  object[]
     */
    public function getCallbacks(): array
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
    protected function registerCallbacks(): void
    {
        self::$_callbacks = $this->xslCallbacks;
        $this->xsltProcessor->registerPHPFunctions(get_class($this) . '::invokeCallback');
    }

    /**
     * invoke a method on a callback class
     *
     * @return  mixed
     * @throws  \stubbles\xml\xsl\XslCallbackException
     * @since   1.5.0
     */
    public static function invokeCallback()
    {
        $arguments = func_get_args();
        if (count($arguments) < 2) {
            throw new XslCallbackException(
                    'To less arguments: need at last two arguments to use callbacks.'
            );
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
     * @return  \stubbles\xml\xsl\XslProcessor
     * @throws  \stubbles\xml\xsl\XslProcessorException
     */
    public function withParameter(string $nameSpace, string $paramName, string $paramValue): self
    {
        if (false === $this->xsltProcessor->setParameter($nameSpace, $paramName, $paramValue)) {
            throw new XslProcessorException(
                    'Could not set parameter ' . $nameSpace . ':' . $paramName
                    . ' with value ' . $paramValue
            );
        }

        if (!isset($this->parameters[$nameSpace])) {
            $this->parameters[$nameSpace] = [];
        }

        $this->parameters[$nameSpace][$paramName] = $paramValue;
        return $this;
    }

    /**
     * set a list of parameters for the given namespace
     *
     * @param   string                $nameSpace  the namespace where the parameters are in
     * @param   array<string,string>  $params     the list of parameters to set: name => value
     * @return  \stubbles\xml\xsl\XslProcessor
     * @throws  \stubbles\xml\xsl\XslProcessorException
     */
    public function withParameters(string $nameSpace, array $params): self
    {
        if (false === $this->xsltProcessor->setParameter($nameSpace, $params)) {
            throw new XslProcessorException('Could not set parameters in ' . $nameSpace);
        }

        if (!isset($this->parameters[$nameSpace])) {
            $this->parameters[$nameSpace] = [];
        }

        $this->parameters[$nameSpace] = array_merge($this->parameters[$nameSpace], $params);
        return $this;
    }

    /**
     * transoforms the document into another DOMDocument
     *
     * @return  \DOMDocument
     * @throws  \stubbles\xml\xsl\XslProcessorException
     */
    public function toDoc(): \DOMDocument
    {
        if (null === $this->document) {
            throw new XslProcessorException(
                    'Can not transform, set document or xml file to transform first'
            );
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
     * @throws  \stubbles\xml\xsl\XslProcessorException
     */
    public function toUri(string $uri): int
    {
        if (null === $this->document) {
            throw new XslProcessorException(
                    'Can not transform, set document or xml file to transform first'
            );
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
     * @throws  \stubbles\xml\xsl\XslProcessorException
     */
    public function toXml(): string
    {
        if (null === $this->document) {
            throw new XslProcessorException(
                    'Can not transform, set document or xml file to transform first'
            );
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
    private function createMessage(): string
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
