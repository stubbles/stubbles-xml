<?php
declare(strict_types=1);
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace stubbles\helper\serializer\annotationbased;
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
     * @XmlFragment(tagName='noXml');
     */
    public string $noXml = 'bar';
    /**
     * another property containing no data
     *
     * @XmlFragment(tagName='noData');
     */
    public ?string $noData = null;

    /**
     * method returnin no valid xml
     *
     * @XmlFragment(tagName=false);
     */
    public function noXml(): string
    {
        return '';
    }
}
