<?php
declare(strict_types=1);
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace stubbles\helper\serializer;

use stubbles\xml\serializer\attributes\XmlAttribute;
use stubbles\xml\serializer\attributes\XmlFragment;
use stubbles\xml\serializer\attributes\XmlTag;

/**
 * Simple example class to test the xml serializer with invalid xml fragments.
 */
#[XmlTag('test')]
class ExampleObjectWithInvalidXmlFragments
{
    /**
     * property containing no XML
     */
    #[XmlFragment('noXml')]
    public string $noXml = 'bar';
    /**
     * another property containing no data
     */
    #[XmlFragment('noData')]
    public ?string $noData = null;

    /**
     * method returnin no valid xml
     */
    #[XmlFragment(tagName:false)]
    public function noXml(): string
    {
        return '';
    }
}
