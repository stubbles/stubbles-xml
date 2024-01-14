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
 * Simple example class to test the xml serializer with xml fragments.
 *
 * @XmlTag(tagName='test')
 */
class ExampleObjectWithXmlFragments
{
    /**
     * property containing XML
     *
     * @XmlFragment(tagName='xml');
     */
    public string $xml = '<foo>bar</foo>';
    /**
     * another property containing XML
     *
     * @XmlFragment(tagName=false);
     */
    public string $xml2 = '<foo>bar</foo>';

    /**
     * method returning xml
     *
     * @XmlFragment(tagName='description', transformNewLineToBr=true);
     */
    public function getSomeXml(): string
    {
        return "foo\nb&ar\n\nbaz";
    }
}
