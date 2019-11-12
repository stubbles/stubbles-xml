<?php
declare(strict_types=1);
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace stubbles\helper\xsl;
use stubbles\xml\xsl\XslProcessor;

/**
 * Helper class for the test.
 */
class TestXslProcessor extends XslProcessor
{
    /**
     * mocked xslt processor
     *
     * @type  \bovigo\callmap\Proxy
     */
    public static $xsltProcessor;

    /**
     * overwrite creation method to inject the mock object
     */
    protected function createXsltProcessor()
    {
        return self::$xsltProcessor;
    }

    /**
     * makes sure callbacks are registered
     */
    public function registerCallbacks()
    {
        parent::registerCallbacks();
    }
}