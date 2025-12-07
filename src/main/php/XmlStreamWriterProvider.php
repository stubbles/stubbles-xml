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
    private array $types    = [
        'dom'       => DomXmlStreamWriter::class,
        'xmlwriter' => LibXmlStreamWriter::class
    ];

    /**
     * constructor
     *
     * @param  array<string,class-string<XmlStreamWriter>>  $types  map of available streamwriter types
     * @Named{types}('stubbles.xml.types')
     * @Named{version}('stubbles.xml.version')
     * @Named{encoding}('stubbles.xml.encoding')
     */
    public function __construct(
            ?array $types = null,
            private string $version = '1.0',
            private string $encoding = 'UTF-8'
    ) {
        if (null !== $types) {
            $this->types = $types;
        }
    }

    public function get(?string $name = null): XmlStreamWriter
    {
        if (null != $name) {
            return $this->createStreamWriter($name);
        }

        return $this->createAsAvailable();
    }

    protected function createStreamWriter(string $xmlExtension): XmlStreamWriter
    {
        $className = $this->types[$xmlExtension];
        return new $className($this->version, $this->encoding);
    }

    /**
     * creates a xml stream writer depending on available xml extensions
     *
     * @throws  XmlException
     */
    protected function createAsAvailable(): XmlStreamWriter
    {
        foreach (array_keys($this->types) as $xmlExtension) {
            if (extension_loaded($xmlExtension)) {
                return $this->createStreamWriter($xmlExtension);
            }
        }

        throw new XmlException(
            'No supported xml extension available, can not create a xml stream writer.'
        );
    }
}
