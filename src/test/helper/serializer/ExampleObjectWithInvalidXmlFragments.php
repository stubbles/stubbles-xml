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
 * Simple example class to test the xml serializer with invalid xml fragments.
 *
 * @XmlTag(tagName='test')
 */
class ExampleObjectWithInvalidXmlFragments
{
    /**
     * property containing no XML
     *
     * @type string
     * @XmlFragment(tagName='noXml');
     */
    public $noXml = 'bar';
    /**
     * another property containing no data
     *
     * @type string
     * @XmlFragment(tagName='noData');
     */
    public $noData;

    /**
     * method returnin no valid xml
     *
     * @return  string
     * @XmlFragment(tagName=false);
     */
    public function noXml(): string
    {
        return '';
    }
}
