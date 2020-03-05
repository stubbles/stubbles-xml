<?php
declare(strict_types=1);
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace stubbles\xml;
use stubbles\ioc\InjectionProvider;
use stubbles\xml\DomXmlStreamWriter;
use stubbles\xml\LibXmlStreamWriter;
/**
 * Provider to create a xml stream writer instances.
 *
 * @since  1.1.0
 * @implements  InjectionProvider<XmlStreamWriter>
 */
class XmlStreamWriterProvider implements InjectionProvider
{
    /**
     * map of available streamwriter types
     *
     * Types has to be a map where the key denotes the required PHP extension
     * and the value the full qualified class name of the XmlStreamWriter
     * implementation.
     *
     * @var  array<string,class-string<XmlStreamWriter>>
     */
    private $types    = [
            'dom'       => DomXmlStreamWriter::class,
            'xmlwriter' => LibXmlStreamWriter::class
    ];
    /**
     * default version of xml stream writers to create
     *
     * @var  string
     */
    private $version;
    /**
     * default encoding of xml stream writers to create
     *
     * @var  string
     */
    private $encoding;

    /**
     * constructor
     *
     * @param  array<string,class-string<XmlStreamWriter>>  $types     optional  map of available streamwriter types
     * @param  string                                       $version   optional  xml version
     * @param  string                                       $encoding  optional  xml encoding
     * @Named{types}('stubbles.xml.types')
     * @Named{version}('stubbles.xml.version')
     * @Named{encoding}('stubbles.xml.encoding')
     */
    public function __construct(
            array $types = null,
            string $version = '1.0',
            string $encoding = 'UTF-8'
    ) {
        if (null !== $types) {
            $this->types = $types;
        }

        $this->version  = $version;
        $this->encoding = $encoding;
    }

    /**
     * returns the value to provide
     *
     * @param   string  $name
     * @return  XmlStreamWriter
     */
    public function get(string $name = null)
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
     * @return  \stubbles\xml\XmlStreamWriter
     */
    protected function createStreamWriter(string $xmlExtension): XmlStreamWriter
    {
        $className = $this->types[$xmlExtension];
        return new $className($this->version, $this->encoding);
    }

    /**
     * creates a xml stream writer depending on available xml extensions
     *
     * @return  \stubbles\xml\XmlStreamWriter
     * @throws  \stubbles\xml\XmlException
     */
    protected function createAsAvailable(): XmlStreamWriter
    {
        foreach (array_keys($this->types) as $xmlExtension) {
            if (extension_loaded($xmlExtension)) {
                return $this->createStreamWriter($xmlExtension);
            }
        }

        throw new XmlException(
                'No supported xml extension available, can not create a xml stream writer!'
        );
    }
}
