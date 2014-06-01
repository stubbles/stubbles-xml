<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  net\stubbles\xml
 */
namespace org\stubbles\test\xml\serializer;
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
     * @type string
     * @XmlFragment(tagName='xml');
     */
    public $xml = '<foo>bar</foo>';
    /**
     * another property containing XML
     *
     * @type string
     * @XmlFragment(tagName=false);
     */
    public $xml2 = '<foo>bar</foo>';

    /**
     * method returning xml
     *
     * @return  string
     * @XmlFragment(tagName='description', transformNewLineToBr=true);
     */
    public function getSomeXml()
    {
        return "foo\nb&ar\n\nbaz";
    }
}
