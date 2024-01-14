<?php
declare(strict_types=1);
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace stubbles\helper\serializer;
/**
 * Simple example class to the xml serializer with german umlaut properties.
 *
 * @XmlTag(tagName='test')
 */
class ExampleObjectWithUmlauts
{
    /**
     * test property
     *
     * @XmlTag(tagName='foo')
     */
    public string $foo = 'Hähnchen';
    /**
     * test attribute property
     *
     * @XmlAttribute(attributeName='bar')
     */
    public string $ba = 'Hähnchen';
}
