<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  stubbles\xml
 */
namespace stubbles\xml;
use stubbles\ioc\InjectionProvider;
/**
 * Provider to create a xml stream writer instances.
 *
 * @since  1.1.0
 */
class XmlStreamWriterProvider implements InjectionProvider
{
    /**
     * list of available streamwriter types
     *
     * @type  array
     */
    protected $types    = ['dom'       => 'stubbles\xml\DomXmlStreamWriter',
                           'xmlwriter' => 'stubbles\xml\LibXmlStreamWriter'
                          ];
    /**
     * default version of xml stream writers to create
     *
     * @type  string
     */
    protected $version  = '1.0';
    /**
     * default encoding of xml stream writers to create
     *
     * @type  string
     */
    protected $encoding = 'UTF-8';

    /**
     * set available xml stream writer types
     *
     * Types has to be a map where the key denotes the required PHP extension
     * and the value the full qualified class name of the XmlStreamWriter
     * implementation.
     *
     * @param   array  $types
     * @return  XmlStreamWriterProvider
     * @Inject(optional=true)
     * @Named('stubbles.xml.types')
     */
    public function setTypes(array $types)
    {
        $this->types = $types;
        return $this;
    }

    /**
     * sets the default version of xml stream writers to create
     *
     * @param   string  $version
     * @return  XmlStreamWriterProvider
     * @Inject(optional=true)
     * @Named('stubbles.xml.version')
     */
    public function setVersion($version)
    {
        $this->version = $version;
        return $this;
    }

    /**
     * sets the default encoding of xml stream writers to create
     *
     * @param   string  $encoding
     * @return  XmlStreamWriterProvider
     * @Inject(optional=true)
     * @Named('stubbles.xml.encoding')
     */
    public function setEncoding($encoding)
    {
        $this->encoding = $encoding;
        return $this;
    }

    /**
     * returns the value to provide
     *
     * @param   string  $name
     * @return  mixed
     */
    public function get($name = null)
    {
        if (null != $name) {
            return $this->createStreamWriter($name);
        }

        return $this->createAsAvailable();
    }

    /**
     * creates a xml stream writer of the given type
     *
     * @param   string  $xmlExtension  concrete type to create
     * @return  XmlStreamWriter
     */
    protected function createStreamWriter($xmlExtension)
    {
        $className = $this->types[$xmlExtension];
        return new $className($this->version, $this->encoding);
    }

    /**
     * creates a xml stream writer depending on available xml extensions
     *
     * @return  XmlStreamWriter
     * @throws  XMLException
     */
    protected function createAsAvailable()
    {
        foreach (array_keys($this->types) as $xmlExtension) {
            if (extension_loaded($xmlExtension)) {
                return $this->createStreamWriter($xmlExtension);
            }
        }

        throw new XmlException('No supported xml extension available, can not create a xml stream writer!');
    }
}
