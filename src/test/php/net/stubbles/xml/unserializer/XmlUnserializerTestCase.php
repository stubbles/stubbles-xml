<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  net\stubbles\xml
 */
namespace net\stubbles\xml\unserializer;
use org\bovigo\vfs\vfsStream;
/**
 * Test for net\stubbles\xml\unserializer\XmlUnserializer.
 *
 * @group  xml
 * @group  xml_unserializer
 */
class XmlUnserializerTestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * test unserializing any XML
     *
     * @test
     */
    public function unserializeAnyXML()
    {
        $xml = '<?xml version="1.0" encoding="iso-8859-1"?>' .
               '<users>' .
               '  <user handle="schst">Stephan Schmidt</user>' .
               '  <user handle="mikey">Frank Kleine</user>' .
               '  <group name="dev">Stubbles Development Team</group>' .
               '  <foo id="test">This is handled by the default keyAttribute</foo>' .
               '  <foo id="test2">Another foo tag</foo>' .
               '</users>';
        $options      = array(XmlUnserializerOption::ATTRIBUTE_KEY => array('user'     => 'handle',
                                                                                'group'    => 'name',
                                                                                '#default' => 'id'
                                                                          )
                        );
        $unserializer = new XmlUnserializer($options);
        $this->assertEquals(array('schst' => 'Stephan Schmidt',
                                  'mikey' => 'Frank Kleine',
                                  'dev'   => 'Stubbles Development Team',
                                  'test'  => 'This is handled by the default keyAttribute',
                                  'test2' => 'Another foo tag'
                            ),
                            $unserializer->unserialize($xml)
        );
    }

    /**
     * test unserializing a list of items
     *
     * @test
     */
    public function unserializeList()
    {
        $xml1 = '<?xml version="1.0" encoding="iso-8859-1"?>
                <root>
                   <item>
                     <name>schst</name>
                   </item>
                   <item>
                     <name>mikey</name>
                   </item>
                 </root>';

        $xml2 = '<?xml version="1.0" encoding="iso-8859-1"?>
                <root>
                   <item>
                     <name>schst</name>
                   </item>
                 </root>';
        $options      = array(XmlUnserializerOption::FORCE_LIST => array('item'));
        $unserializer = new XmlUnserializer($options);
        $this->assertEquals(array('item' => array(array('name' => 'schst'),
                                                  array('name' => 'mikey')
                                            )
                            ),
                            $unserializer->unserialize($xml1)
        );
        $this->assertEquals(array('item' => array(array('name' => 'schst'))), $unserializer->unserialize($xml2));
    }

    /**
     * test that whitespace handling works as expected
     *
     * @test
     */
    public function whiteSpaceTrim()
    {
        $xml = '<?xml version="1.0" encoding="iso-8859-1"?>
                <xml>
                  <string>

                    This XML
                    document
                    contains
                    line breaks.

                  </string>
                </xml>';
        $options      = array(XmlUnserializerOption::WHITESPACE => XmlUnserializerOption::WHITESPACE_TRIM);
        $unserializer = new XmlUnserializer($options);
        $this->assertEquals(array('string' => 'This XML
                    document
                    contains
                    line breaks.'),
                            $unserializer->unserialize($xml)
        );
    }

    /**
     * test that whitespace handling works as expected
     *
     * @test
     */
    public function whiteSpaceNormalize()
    {
        $xml = '<?xml version="1.0" encoding="iso-8859-1"?>
                <xml>
                  <string>

                    This XML
                    document
                    contains
                    line breaks.

                  </string>
                </xml>';
        $options      = array(XmlUnserializerOption::WHITESPACE => XmlUnserializerOption::WHITESPACE_NORMALIZE);
        $unserializer = new XmlUnserializer($options);
        $this->assertEquals(array('string' => 'This XML document contains line breaks.'), $unserializer->unserialize($xml));
    }

    /**
     * test that whitespace handling works as expected
     *
     * @test
     */
    public function whiteSpaceKeep()
    {
        $xml = '<?xml version="1.0" encoding="iso-8859-1"?>
                <xml>
                  <string>

                    This XML
                    document
                    contains
                    line breaks.

                  </string>
                </xml>';
        $options      = array(XmlUnserializerOption::WHITESPACE => XmlUnserializerOption::WHITESPACE_KEEP);
        $unserializer = new XmlUnserializer($options);
        $this->assertEquals(array('string' => '

                    This XML
                    document
                    contains
                    line breaks.

                  '),
                           $unserializer->unserialize($xml)
        );
    }

    /**
     * test unserializing a list of items
     *
     * @test
     */
    public function unserializeWithAttributes()
    {
        $root = vfsStream::setup();
        vfsStream::newFile('unserializer.xml')
                 ->withContent('<xml>
    <test foo="bar">
        Test
        <tag>test</tag>
    </test>
</xml>')
                 ->at($root);
        $options      = array(XmlUnserializerOption::ATTRIBUTES_PARSE    => true,
                              XmlUnserializerOption::ATTRIBUTES_ARRAYKEY => false
                        );
        $unserializer = new XmlUnserializer($options);
        $this->assertEquals(array('test' => array('foo'      => 'bar',
                                                  'tag'      => 'test',
                                                  '_content' => 'Test'
                                            )
                            ),
                            $unserializer->unserializeFile(vfsStream::url('root/unserializer.xml'))
        );
    }

    /**
     * test unserializing a list of items
     *
     * @test
     */
    public function unserializeWithTagMap()
    {
        $xml1         = '<?xml version="1.0" encoding="iso-8859-1"?>' .
                        '<root>' .
                        '  <foo>FOO</foo>' .
                        '  <bar>BAR</bar>' .
                        '</root>';
        $xml2         = '<?xml version="1.0" encoding="iso-8859-1"?>' .
                        '<root>' .
                        '  <foo>'.
                        '    <tomato>45</tomato>'.
                        '  </foo>'.
                        '  <bar>'.
                        '    <tomato>31</tomato>'.
                        '  </bar>'.
                        '</root>';
        $options      = array(XmlUnserializerOption::TAG_MAP => array('foo' => 'bar',
                                                                          'bar' => 'foo'
                                                                    )
                        );
        $unserializer = new XmlUnserializer($options);
        $this->assertEquals(array('bar' => 'FOO',
                                  'foo' => 'BAR'
                            ),
                            $unserializer->unserialize($xml1)
        );
        $this->assertEquals(array('bar' => array('tomato' => 45),
                                  'foo' => array('tomato' => 31)
                            ),
                            $unserializer->unserialize($xml2)
        );
    }

    /**
     * test unserializing a list of items
     *
     * @test
     */
    public function unserializeWithTypeGuessing()
    {
        $xml          = '<?xml version="1.0" encoding="iso-8859-1"?>' .
                        '<root>' .
                        '  <string>Just a string...</string>' .
                        '  <booleanValue>true</booleanValue>' .
                        '  <foo>-563</foo>' .
                        '  <bar>4.73736</bar>' .
                        '  <array foo="false" bar="12">true</array>' .
                        '</root>';
        $options      = array(XmlUnserializerOption::ATTRIBUTES_PARSE => true,
                              XmlUnserializerOption::GUESS_TYPES      => true
                        );
        $unserializer = new XmlUnserializer($options);
        $result       = $unserializer->unserialize($xml);
        $this->assertEquals(array('string'       => 'Just a string...',
                                  'booleanValue' => true,
                                  'foo'          => -563,
                                  'bar'          => 4.73736,
                                  'array'        => array('foo'      => false,
                                                          'bar'      => 12,
                                                          '_content' => true
                                                    )
                             ),
                             $result
        );
        $this->assertTrue($result['booleanValue']);
        $this->assertTrue(is_int($result['foo']));
        $this->assertTrue(is_float($result['bar']));
        $this->assertFalse($result['array']['foo']);
        $this->assertTrue(is_int($result['array']['bar']));
        $this->assertTrue($result['array']['_content']);
    }

    /**
     * assert that output encoding is UTF-8
     *
     * @test
     */
    public function returnEncoding()
    {
        $xml          = '<?xml version="1.0" encoding="iso-8859-1"?><root><string>A string containing german umlauts: ' . utf8_decode('äöü') . '</string></root>';
        $unserializer = new XmlUnserializer();
        $this->assertEquals(array('string' => utf8_encode('A string containing german umlauts: ' . utf8_decode('äöü'))), $unserializer->unserialize($xml));
    }

    /**
     * assert that cdata is supported
     *
     * @test
     */
    public function cDATA()
    {
        $xml          = '<?xml version="1.0" encoding="iso-8859-1"?><root><string><![CDATA[A string containing german umlauts: &' . utf8_decode('äöü') . ']]></string></root>';
        $unserializer = new XmlUnserializer();
        $this->assertEquals(array('string' => utf8_encode('A string containing german umlauts: &' . utf8_decode('äöü'))), $unserializer->unserialize($xml));
    }

    /**
     * @test
     * @expectedException  net\stubbles\lang\exception\FileNotFoundException
     */
    public function unserializeNonExistingFileThrowsFileNotFoundException()
    {
        vfsStream::setup();
        $unserializer = new XmlUnserializer();
        $unserializer->unserializeFile(vfsStream::url('root/doesNotExist.xml'));
    }
}
?>