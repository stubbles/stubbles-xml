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
use stubbles\xml\serializer\attributes\XmlTag;

/**
 * Simple example class to test the xml serializer with empty attribute values.
 */
#[XmlTag('test')]
class ExampleObjectClassWithEmptyAttributes
{
    /**
     * Empty property
     */
    #[XmlAttribute('emptyProp')]
    public mixed $emptyProp = null;
    /**
     * Empty property
     */
    #[XmlAttribute('emptyProp2', skipEmpty:false)]
    public mixed $emptyProp2 = null;

    /**
     * Empty return value
     */
    #[XmlAttribute('emptyMethod')]
    public function getEmpty(): mixed {
        return null;
    }

    /**
     * Empty return value
     */
    #[XmlAttribute('emptyMethod2', skipEmpty:false)]
    public function getEmpty2(): mixed {
        return null;
    }
}
