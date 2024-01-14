<?php
declare(strict_types=1);
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace stubbles\helper\xsl;

use bovigo\callmap\ClassProxy;
use stubbles\xml\xsl\XslProcessor;
use XSLTProcessor;

/**
 * Helper class for the test.
 */
class ExtendedXslProcessor extends XslProcessor
{
    public static XSLTProcessor&ClassProxy $xsltProcessor;

    /**
     * overwrite creation method to inject the mock object
     */
    protected function createXsltProcessor(): XSLTProcessor&ClassProxy
    {
        return self::$xsltProcessor;
    }

    /**
     * makes sure callbacks are registered
     */
    public function callRegisterCallbacks()
    {
        parent::registerCallbacks();
    }
}