<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  stubbles\xml
 */
namespace stubbles\xml\serializer;
use bovigo\callmap\NewInstance;
require_once __DIR__ . '/examples/ContainerWithArrayListTagName.php';
require_once __DIR__ . '/examples/ContainerWithArrayListWithoutTagName.php';
require_once __DIR__ . '/examples/ContainerWithIterator.php';
require_once __DIR__ . '/examples/ExampleObjectClass.php';
require_once __DIR__ . '/examples/ExampleObjectClassWithEmptyAttributes.php';
require_once __DIR__ . '/examples/ExampleObjectClassWithMethods.php';
require_once __DIR__ . '/examples/ExampleObjectClassWithSerializer.php';
require_once __DIR__ . '/examples/ExampleObjectSerializer.php';
require_once __DIR__ . '/examples/ExampleObjectWithInvalidXmlFragments.php';
require_once __DIR__ . '/examples/ExampleObjectWithUmlauts.php';
require_once __DIR__ . '/examples/ExampleObjectWithXmlFragments.php';
require_once __DIR__ . '/examples/ExampleStaticClass.php';
use org\stubbles\test\xml\serializer\ContainerWithArrayListTagName;
use org\stubbles\test\xml\serializer\ContainerWithArrayListWithoutTagName;
use org\stubbles\test\xml\serializer\ContainerWithIterator;
use org\stubbles\test\xml\serializer\ExampleObjectClass;
use org\stubbles\test\xml\serializer\ExampleObjectClassWithEmptyAttributes;
use org\stubbles\test\xml\serializer\ExampleObjectClassWithMethods;
use org\stubbles\test\xml\serializer\ExampleObjectClassWithSerializer;
use org\stubbles\test\xml\serializer\ExampleObjectSerializer;
use org\stubbles\test\xml\serializer\ExampleObjectWithInvalidXmlFragments;
use org\stubbles\test\xml\serializer\ExampleObjectWithUmlauts;
use org\stubbles\test\xml\serializer\ExampleObjectWithXmlFragments;
use org\stubbles\test\xml\serializer\ExampleStaticClass;
use stubbles\lang\Sequence;
use stubbles\lang\reflect;
use stubbles\xml\DomXmlStreamWriter;
/**
 * Test for stubbles\xml\serializer\XmlSerializer.
 *
 * @group  xml
 * @group  xml_serializer
 */
class XmlSerializerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * instance to test
     *
     * @type XmlSerializer
     */
    private $serializer;
    /**
     * mocked injector instance
     *
     * @type  \bovigo\callmap\Proxy
     */
    private $injector;

    /**
     * set up test environment
     */
    public function setUp()
    {
        libxml_clear_errors();
        $this->injector   = NewInstance::stub('stubbles\ioc\Injector');
        $this->serializer = new XmlSerializer($this->injector);
    }

    /**
     * clean up test environment
     */
    public function tearDown()
    {
        libxml_clear_errors();
    }

    /**
     *
     * @param   mixed   $value
     * @param   string  $tagName         optional  name of the surrounding xml tag
     * @param   string  $elementTagName  optional  recurring element tag name for lists
     * @return  string
     */
    protected function serialize($value, $tagName = null, $elementTagName = null)
    {
        return $this->serializer->serialize(
                $value,
                new DomXmlStreamWriter(),
                $tagName,
                $elementTagName
        )->asXml();
    }

    /**
     * adds prefix to given xml string
     *
     * @param   string  $xml
     * @return  string
     */
    protected function prefixXml($xml)
    {
        return '<?xml version="1.0" encoding="UTF-8"?>' . "\n" . $xml;
    }

    /**
     * @test
     */
    public function serializeNullWithoutTagName()
    {
        assertEquals(
                $this->prefixXml('<null><null/></null>'),
                $this->serialize(null)
        );
    }

    /**
     * @test
     */
    public function serializeNullWithGivenTagName()
    {
        assertEquals(
                $this->prefixXml('<root><null/></root>'),
                $this->serialize(null, 'root')
        );
    }

    /**
     * @test
     */
    public function serializeBooleanTrueWithoutTagName()
    {
        assertEquals(
                $this->prefixXml('<boolean>true</boolean>'),
                $this->serialize(true)
        );
    }

    /**
     * @test
     */
    public function serializeBooleanTrueWithGivenTagName()
    {
        assertEquals(
                $this->prefixXml('<root>true</root>'),
                $this->serialize(true, 'root')
        );
    }

    /**
     * @test
     */
    public function serializeBooleanFalseWithoutTagName()
    {
        assertEquals(
                $this->prefixXml('<boolean>false</boolean>'),
                $this->serialize(false)
        );
    }

    /**
     * @test
     */
    public function serializeBooleanFalseWithGivenTagName()
    {
        assertEquals(
                $this->prefixXml('<root>false</root>'),
                $this->serialize(false, 'root')
        );
    }

    /**
     * @test
     */
    public function serializeStringWithoutTagName()
    {
        assertEquals(
                $this->prefixXml('<string>This is a string.</string>'),
                $this->serialize('This is a string.')
        );
    }

    /**
     * @test
     */
    public function serializeStringWithGivenTagName()
    {
        assertEquals(
                $this->prefixXml('<root>This is a string.</root>'),
                $this->serialize('This is a string.', 'root')
        );
    }

    /**
     * @test
     */
    public function serializeStringWithoutTagNameDirectly()
    {
        assertEquals(
                $this->prefixXml('<string>This is a string.</string>'),
                $this->serializer->serializeString(
                        'This is a string.',
                        new DomXmlStreamWriter()
                )->asXml()
        );
    }

    /**
     * @test
     */
    public function serializeStringWithGivenTagNameDirectly()
    {
        assertEquals(
                $this->prefixXml('<root>This is a string.</root>'),
                $this->serializer->serializeString(
                        'This is a string.',
                        new DomXmlStreamWriter(),
                        'root'
                )->asXml()
        );
    }

    /**
     * @test
     */
    public function serializeIntegerWithoutTagName()
    {
        assertEquals(
                $this->prefixXml('<integer>45</integer>'),
                $this->serialize(45)
        );
    }

    /**
     * @test
     */
    public function serializeIntegerWithGivenTagName()
    {
        assertEquals(
                $this->prefixXml('<root>45</root>'),
                $this->serialize(45, 'root')
        );
    }

    /**
     * @test
     */
    public function serializeIntegerWithoutTagNameDirectly()
    {
        assertEquals(
                $this->prefixXml('<integer>45</integer>'),
                $this->serializer->serializeInt(45, new DomXmlStreamWriter())
                        ->asXml()
        );
    }

    /**
     * @test
     */
    public function serializeIntegerWithGivenTagNameDirectly()
    {
        assertEquals(
                $this->prefixXml('<root>45</root>'),
                $this->serializer->serializeInt(45, new DomXmlStreamWriter(), 'root')
                        ->asXml()
        );
    }

    /**
     * @test
     */
    public function serializeFloatWithoutTagName()
    {
        assertEquals(
                $this->prefixXml('<double>2.352</double>'),
                $this->serialize(2.352)
        );
    }

    /**
     * @test
     */
    public function serializeFloatWithGivenTagName()
    {
        assertEquals(
                $this->prefixXml('<root>2.352</root>'),
                $this->serialize(2.352, 'root')
        );
    }

    /**
     * @test
     */
    public function serializeFloatWithoutTagNameDirectly()
    {
        assertEquals(
                $this->prefixXml('<double>2.352</double>'),
                $this->serializer->serializeFloat(
                        2.352,
                        new DomXmlStreamWriter()
                )->asXml()
        );
    }

    /**
     * @test
     */
    public function serializeFloatWithGivenTagNameDirectly()
    {
        assertEquals(
                $this->prefixXml('<root>2.352</root>'),
                $this->serializer->serializeFloat(
                        2.352,
                        new DomXmlStreamWriter(),
                        'root'
                )->asXml()
        );
    }

    /**
     * @test
     */
    public function serializeAssociativeArrayWithoutTagName()
    {
        assertEquals(
                $this->prefixXml('<array><one>two</one><three>four</three></array>'),
                $this->serialize(['one' => 'two', 'three' => 'four'])
        );
    }

    /**
     * @test
     */
    public function serializeAssociativeArrayWithGivenTagName()
    {
        assertEquals(
                $this->prefixXml('<root><one>two</one><three>four</three></root>'),
                $this->serialize(
                        ['one'   => 'two',
                         'three' => 'four'
                        ],
                        'root'
                )
        );
    }

    /**
     * @test
     */
    public function serializeIndexedArrayWithoutTagName()
    {
        assertEquals(
                $this->prefixXml('<array><string>one</string><integer>2</integer><string>three</string></array>'),
                $this->serialize(['one', 2, 'three'])
        );
    }

    /**
     * @test
     */
    public function serializeIndexedArrayWithGivenTagName()
    {
        assertEquals(
                $this->prefixXml('<root><string>one</string><integer>2</integer><string>three</string></root>'),
                $this->serialize(['one', 2, 'three'], 'root')
        );
    }

    /**
     * @test
     */
    public function serializeIndexedArrayWithoutTagNameAndGivenElementTagName()
    {
        assertEquals(
                $this->prefixXml('<array><foo>one</foo><foo>2</foo><foo>three</foo></array>'),
                $this->serialize(['one', 2, 'three'], null, 'foo')
        );
    }

    /**
     * @test
     */
    public function serializeIndexedArrayWithGivenTagNameAndElementTagName()
    {
        assertEquals(
                $this->prefixXml('<root><foo>one</foo><foo>2</foo><foo>three</foo></root>'),
                $this->serialize(['one', 2, 'three'], 'root', 'foo')
        );
    }

    /**
     * @test
     */
    public function serializeNestedArray()
    {
        assertEquals(
                $this->prefixXml('<root><one>two</one><three><four>five</four></three></root>'),
                $this->serialize(
                        ['one'   => 'two',
                         'three' => ['four' => 'five']
                        ],
                        'root'
                )
        );
    }

    /**
     * @test
     */
    public function serializeAssociativeIteratorWithoutTagName()
    {
        assertEquals(
                $this->prefixXml('<array><one>two</one><three>four</three></array>'),
                $this->serialize(
                        new \ArrayIterator(['one' => 'two', 'three' => 'four'])
                )
        );
    }

    /**
     * @test
     */
    public function serializeAssociativeIteratorWithGivenTagName()
    {
        assertEquals(
                $this->prefixXml('<root><one>two</one><three>four</three></root>'),
                $this->serialize(
                        new \ArrayIterator(['one' => 'two', 'three' => 'four']),
                        'root'
                )
        );
    }

    /**
     * @test
     */
    public function serializeIndexedIteratorWithoutTagName()
    {
        assertEquals(
                $this->prefixXml('<array><string>one</string><integer>2</integer><string>three</string></array>'),
                $this->serialize(new \ArrayIterator(['one', 2, 'three']))
        );
    }

    /**
     * @test
     */
    public function serializeIndexedIteratorWithGivenTagName()
    {
        assertEquals(
                $this->prefixXml('<root><string>one</string><integer>2</integer><string>three</string></root>'),
                $this->serialize(
                        new \ArrayIterator(['one', 2, 'three']),
                        'root'
                )
        );
    }

    /**
     * @test
     */
    public function serializeIndexedIteratorWithGivenTagNameAndElementTagName()
    {
        assertEquals(
                $this->prefixXml('<root><foo>one</foo><foo>2</foo><foo>three</foo></root>'),
                $this->serialize(
                        new \ArrayIterator(['one', 2, 'three']),
                        'root',
                        'foo'
                )
        );
    }

    /**
     * @test
     */
    public function serializeNestedIterator()
    {
        assertEquals(
                $this->prefixXml('<root><one>two</one><three><four>five</four></three></root>'),
                $this->serialize(
                        new \ArrayIterator(
                                ['one'   => 'two',
                                 'three' => new \ArrayIterator(['four' => 'five'])
                                ]
                        ),
                        'root'
                )
        );
    }

    /**
     * @test
     * @since  4.2.0
     */
    public function serializeAssociativeSequenceWithoutTagName()
    {
        assertEquals(
                $this->prefixXml('<array><one>two</one><three>four</three></array>'),
                $this->serialize(Sequence::of(['one' => 'two', 'three' => 'four']))
        );
    }

    /**
     * @test
     * @since  4.2.0
     */
    public function serializeAssociativeSequenceWithGivenTagName()
    {
        assertEquals(
                $this->prefixXml('<root><one>two</one><three>four</three></root>'),
                $this->serialize(
                        Sequence::of(
                                ['one'   => 'two',
                                 'three' => 'four'
                                ]
                        ),
                        'root'
                )
        );
    }

    /**
     * @test
     * @since  4.2.0
     */
    public function serializeIndexedSequenceWithoutTagName()
    {
        assertEquals(
                $this->prefixXml('<array><string>one</string><integer>2</integer><string>three</string></array>'),
                $this->serialize(Sequence::of(['one', 2, 'three']))
        );
    }

    /**
     * @test
     * @since  4.2.0
     */
    public function serializeIndexedSequenceWithGivenTagName()
    {
        assertEquals(
                $this->prefixXml('<root><string>one</string><integer>2</integer><string>three</string></root>'),
                $this->serialize(Sequence::of(['one', 2, 'three']), 'root')
        );
    }

    /**
     * @test
     * @since  4.2.0
     */
    public function serializeIndexedSequenceWithoutTagNameAndGivenElementTagName()
    {
        assertEquals(
                $this->prefixXml('<array><foo>one</foo><foo>2</foo><foo>three</foo></array>'),
                $this->serialize(Sequence::of(['one', 2, 'three']), null, 'foo')
        );
    }

    /**
     * @test
     * @since  4.2.0
     */
    public function serializeIndexedSequenceWithGivenTagNameAndElementTagName()
    {
        assertEquals(
                $this->prefixXml('<root><foo>one</foo><foo>2</foo><foo>three</foo></root>'),
                $this->serialize(Sequence::of(['one', 2, 'three']), 'root', 'foo')
        );
    }

    /**
     * @test
     * @since  4.2.0
     */
    public function serializeFinalSequence()
    {
        assertEquals(
                $this->prefixXml('<root><one>TWO</one><three>FOUR</three></root>'),
                $this->serialize(
                        Sequence::of(
                            ['one'   => 'two', 'three' => 'four']
                        )->map(function($value) { return strtoupper($value); }),
                        'root'
                )
        );
    }

    /**
     * @test
     */
    public function serializeObjectWithoutTagName()
    {
        assertEquals(
                $this->prefixXml('<foo bar="test"><bar>42</bar></foo>'),
                $this->serialize(new ExampleObjectClass())
        );
    }

    /**
     * @test
     */
    public function serializeObjectWithGivenTagName()
    {
        assertEquals(
                $this->prefixXml('<baz bar="test"><bar>42</bar></baz>'),
                $this->serialize(new ExampleObjectClass(), 'baz')
        );
    }

    /**
     * @test
     */
    public function serializeObjectWithXmlSerializerAnnotation()
    {
        $this->injector->mapCalls(['getInstance' => new ExampleObjectSerializer()]);
        assertEquals(
                $this->prefixXml('<example sound="303"><anything>something</anything></example>'),
                $this->serialize(new ExampleObjectClassWithSerializer())
        );
    }

    /**
     * @test
     */
    public function serializeNestedObject()
    {
        $obj      = new ExampleObjectClass();
        $obj->bar = new ExampleObjectClass();
        assertEquals(
                $this->prefixXml('<foo bar="test"><bar bar="test"><bar>42</bar></bar></foo>'),
                $this->serialize($obj)
        );
    }

    /**
     * @test
     */
    public function serializeObjectWhichContainsArray()
    {
        assertEquals(
                $this->prefixXml('<container><list><item>one</item><item>two</item><item>three</item></list></container>'),
                $this->serialize(new ContainerWithArrayListTagName())
        );
    }

    /**
     * @test
     */
    public function serializeObjectWhichContainsArrayWhereArrayTagNameIsDisabled()
    {
        assertEquals(
                $this->prefixXml('<container><item>one</item><item>two</item><item>three</item></container>'),
                $this->serialize(new ContainerWithArrayListWithoutTagName())
        );
    }

    /**
     * @test
     */
    public function serializeObjectWhichContainsIterator()
    {
        assertEquals(
                $this->prefixXml('<container><item>one</item><item>two</item><item>three</item></container>'),
                $this->serialize(new ContainerWithIterator())
        );
    }

    /**
     * @test
     */
    public function serializeStandardObject()
    {
        assertEquals(
                $this->prefixXml('<class method="returned" isFoo="true" isBar="false"><getBaz>baz</getBaz></class>'),
                $this->serialize(new ExampleObjectClassWithMethods())
        );
    }

    /**
     * @test
     */
    public function serializeObjectWithXmlFragment()
    {
        assertEquals(
                $this->prefixXml('<test><xml><foo>bar</foo></xml><foo>bar</foo><description>foo<br/>' . "\n" . 'b&amp;ar<br/>' . "\n" . '<br/>' . "\n" . 'baz</description></test>'),
                $this->serialize(new ExampleObjectWithXmlFragments())
        );
    }

    /**
     * @test
     */
    public function serializeObjectWithInvalidXmlFragment()
    {
        assertEquals(
                $this->prefixXml('<test><noXml>bar</noXml><noData/></test>'),
                $this->serialize(new ExampleObjectWithInvalidXmlFragments())
        );
    }

    /**
     * @test
     */
    public function serializeObjectWithEmptyAttributes()
    {
        assertEquals(
                $this->prefixXml('<test emptyProp2="" emptyMethod2=""/>'),
                $this->serialize(new ExampleObjectClassWithEmptyAttributes())
        );
    }

    /**
     * @test
     */
    public function doesNotSerializeStaticPropertiesAndMethods()
    {
        assertEquals(
                $this->prefixXml('<ExampleStaticClass/>'),
                $this->serialize(new ExampleStaticClass())
        );
    }

    /**
     * @test
     */
    public function serializeObjectContainingUmlauts()
    {
        assertEquals(
                $this->prefixXml('<test bar="Hähnchen"><foo>Hähnchen</foo></test>'),
                $this->serialize(new ExampleObjectWithUmlauts())
        );
    }

    /**
     * @test
     */
    public function doesNotSerializeResources()
    {
        $fp = fopen(__FILE__, 'rb');
        assertEquals('<?xml version="1.0" encoding="UTF-8"?>',
                            $this->serialize($fp)
        );
        fclose($fp);
    }
}
