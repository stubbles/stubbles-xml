<?php
declare(strict_types=1);
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace stubbles\xml;

use Exception;

/**
 * XML Exception
 */
class XmlException extends Exception
{
    public function __construct(string $message, ?Exception $cause = null)
    {
        parent::__construct($message, 0, $cause);
    }
}
