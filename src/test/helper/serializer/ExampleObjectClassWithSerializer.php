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
 * Simple example class to test the xml serializer with an annotated serializer class.
 *
 * @XmlSerializer(stubbles\helper\serializer\ExampleObjectSerializer.class)
 */
class ExampleObjectClassWithSerializer
{
    /**
     * a property
     */
    public int $bar    = 303;
    /**
     * another property
     */
    public string $scalar = 'not interesting';

    /**
     * returns something
     */
    public function getSomething(): string
    {
        return 'something';
    }
}
