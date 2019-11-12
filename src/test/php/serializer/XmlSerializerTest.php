<?php
declare(strict_types=1);
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace stubbles\xml\serializer;
use bovigo\callmap\NewInstance;
use PHPUnit\Framework\TestCase;
use stubbles\helper\serializer\ContainerWithArrayListTagName;
use stubbles\helper\serializer\ContainerWithArrayListWithoutTagName;
use stubbles\helper\serializer\ContainerWithIterator;
use stubbles\helper\serializer\ExampleObjectClass;
use stubbles\helper\serializer\ExampleObjectClassWithEmptyAttributes;
use stubbles\helper\serializer\ExampleObjectClassWithMethods;
use stubbles\helper\serializer\ExampleObjectClassWithSerializer;
use stubbles\helper\serializer\ExampleObjectSerializer;
use stubbles\helper\serializer\ExampleObjectWithInvalidXmlFragments;
use stubbles\helper\serializer\ExampleObjectWithUmlauts;
use stubbles\helper\serializer\ExampleObjectWithXmlFragments;
use stubbles\helper\serializer\ExampleStaticClass;
use stubbles\helper\serializer\TraversableNonTraversable;
use stubbles\helper\serializer\TraversableTraversable;
use stubbles\ioc\Injector;
use stubbles\sequence\Sequence;
use stubbles\xml\DomXmlStreamWriter;

use function bovigo\assert\assertThat;
use function bovigo\assert\expect;
use function bovigo\assert\predicate\equals;
/**
 * Test for stubbles\xml\serializer\XmlSerializer.
 *
 * @group  xml
 * @group  xml_serializer
 */
class XmlSerializerTest extends TestCase
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

    protected function setUp(): void
    {
        libxml_clear_errors();
        $this->injector   = NewInstance::stub(Injector::class);
        $this->serializer = new XmlSerializer($this->injector);
    }

    protected function tearDown(): void
    {
        libxml_clear_errors();
    }

    protected function serialize($value, string $tagName = null, string $elementTagName = null): string
    {
        return $this->serializer->serialize(
                $value,
                new DomXmlStreamWriter(),
                $tagName,
                $elementTagName
        )->asXml();
    }

    protected function prefixXml(string $xml): string
    {
        return '<?xml version="1.0" encoding="UTF-8"?>' . "\n" . $xml;
    }

    /**
     * @test
     */
    public function serializeNullWithoutTagName()
    {
        assertThat(
                $this->serialize(null),
                equals($this->prefixXml('<null><null/></null>'))
        );
    }

    /**
     * @test
     */
    public function serializeNullWithGivenTagName()
    {
        assertThat(
                $this->serialize(null, 'root'),
                equals($this->prefixXml('<root><null/></root>'))
        );
    }

    /**
     * @test
     */
    public function serializeBooleanTrueWithoutTagName()
    {
        assertThat(
                $this->serialize(true),
                equals($this->prefixXml('<boolean>true</boolean>'))
        );
    }

    /**
     * @test
     */
    public function serializeBooleanTrueWithGivenTagName()
    {
        assertThat(
                $this->serialize(true, 'root'),
                equals($this->prefixXml('<root>true</root>'))
        );
    }

    /**
     * @test
     */
    public function serializeBooleanFalseWithoutTagName()
    {
        assertThat(
                $this->serialize(false),
                equals($this->prefixXml('<boolean>false</boolean>'))
        );
    }

    /**
     * @test
     */
    public function serializeBooleanFalseWithGivenTagName()
    {
        assertThat(
                $this->serialize(false, 'root'),
                equals($this->prefixXml('<root>false</root>'))
        );
    }

    /**
     * @test
     */
    public function serializeStringWithoutTagName()
    {
        assertThat(
                $this->serialize('This is a string.'),
                equals($this->prefixXml('<string>This is a string.</string>'))
        );
    }

    /**
     * @test
     */
    public function serializeStringWithGivenTagName()
    {
        assertThat(
                $this->serialize('This is a string.', 'root'),
                equals($this->prefixXml('<root>This is a string.</root>'))
        );
    }

    /**
     * @test
     */
    public function serializeStringWithoutTagNameDirectly()
    {
        assertThat(
                $this->serializer->serializeString(
                        'This is a string.',
                        new DomXmlStreamWriter()
                )->asXml(),
                equals($this->prefixXml('<string>This is a string.</string>'))
        );
    }

    /**
     * @test
     */
    public function serializeStringWithGivenTagNameDirectly()
    {
        assertThat(
                $this->serializer->serializeString(
                        'This is a string.',
                        new DomXmlStreamWriter(),
                        'root'
                )->asXml(),
                equals($this->prefixXml('<root>This is a string.</root>'))
        );
    }

    /**
     * @test
     */
    public function serializeIntegerWithoutTagName()
    {
        assertThat(
                $this->serialize(45),
                equals($this->prefixXml('<integer>45</integer>'))
        );
    }

    /**
     * @test
     */
    public function serializeIntegerWithGivenTagName()
    {
        assertThat(
                $this->serialize(45, 'root'),
                equals($this->prefixXml('<root>45</root>'))
        );
    }

    /**
     * @test
     */
    public function serializeIntegerWithoutTagNameDirectly()
    {
        assertThat(
                $this->serializer->serializeInt(45, new DomXmlStreamWriter())
                        ->asXml(),
                equals($this->prefixXml('<integer>45</integer>'))
        );
    }

    /**
     * @test
     */
    public function serializeIntegerWithGivenTagNameDirectly()
    {
        assertThat(
                $this->serializer->serializeInt(45, new DomXmlStreamWriter(), 'root')
                        ->asXml(),
                equals($this->prefixXml('<root>45</root>'))
        );
    }

    /**
     * @test
     */
    public function serializeFloatWithoutTagName()
    {
        assertThat(
                $this->serialize(2.352),
                equals($this->prefixXml('<double>2.352</double>'))
        );
    }

    /**
     * @test
     */
    public function serializeFloatWithGivenTagName()
    {
        assertThat(
                $this->serialize(2.352, 'root'),
                equals($this->prefixXml('<root>2.352</root>'))
        );
    }

    /**
     * @test
     */
    public function serializeFloatWithoutTagNameDirectly()
    {
        assertThat(
                $this->serializer->serializeFloat(
                        2.352,
                        new DomXmlStreamWriter()
                )->asXml(),
                equals($this->prefixXml('<double>2.352</double>'))
        );
    }

    /**
     * @test
     */
    public function serializeFloatWithGivenTagNameDirectly()
    {
        assertThat(
                $this->serializer->serializeFloat(
                        2.352,
                        new DomXmlStreamWriter(),
                        'root'
                )->asXml(),
                equals($this->prefixXml('<root>2.352</root>'))
        );
    }

    /**
     * @test
     */
    public function serializeAssociativeArrayWithoutTagName()
    {
        assertThat(
                $this->serialize(['one' => 'two', 'three' => 'four']),
                equals($this->prefixXml('<array><one>two</one><three>four</three></array>'))
        );
    }

    /**
     * @test
     */
    public function serializeAssociativeArrayWithGivenTagName()
    {
        assertThat(
                $this->serialize(
                        ['one'   => 'two',
                         'three' => 'four'
                        ],
                        'root'
                ),
                equals($this->prefixXml('<root><one>two</one><three>four</three></root>'))
        );
    }

    /**
     * @test
     */
    public function serializeIndexedArrayWithoutTagName()
    {
        assertThat(
                $this->serialize(['one', 2, 'three']),
                equals($this->prefixXml('<array><string>one</string><integer>2</integer><string>three</string></array>'))
        );
    }

    /**
     * @test
     */
    public function serializeIndexedArrayWithGivenTagName()
    {
        assertThat(
                $this->serialize(['one', 2, 'three'], 'root'),
                equals($this->prefixXml('<root><string>one</string><integer>2</integer><string>three</string></root>'))
        );
    }

    /**
     * @test
     */
    public function serializeIndexedArrayWithoutTagNameAndGivenElementTagName()
    {
        assertThat(
                $this->serialize(['one', 2, 'three'], null, 'foo'),
                equals($this->prefixXml('<array><foo>one</foo><foo>2</foo><foo>three</foo></array>'))
        );
    }

    /**
     * @test
     */
    public function serializeIndexedArrayWithGivenTagNameAndElementTagName()
    {
        assertThat(
                $this->serialize(['one', 2, 'three'], 'root', 'foo'),
                equals($this->prefixXml('<root><foo>one</foo><foo>2</foo><foo>three</foo></root>'))
        );
    }

    /**
     * @test
     */
    public function serializeNestedArray()
    {
        assertThat(
                $this->serialize(
                        ['one'   => 'two',
                         'three' => ['four' => 'five']
                        ],
                        'root'
                ),
                equals($this->prefixXml('<root><one>two</one><three><four>five</four></three></root>'))
        );
    }

    /**
     * @test
     */
    public function serializeAssociativeIteratorWithoutTagName()
    {
        assertThat(
                $this->serialize(
                        new \ArrayIterator(['one' => 'two', 'three' => 'four'])
                ),
                equals($this->prefixXml('<array><one>two</one><three>four</three></array>'))
        );
    }

    /**
     * @test
     */
    public function serializeAssociativeIteratorWithGivenTagName()
    {
        assertThat(
                $this->serialize(
                        new \ArrayIterator(['one' => 'two', 'three' => 'four']),
                        'root'
                ),
                equals($this->prefixXml('<root><one>two</one><three>four</three></root>'))
        );
    }

    /**
     * @test
     */
    public function serializeIndexedIteratorWithoutTagName()
    {
        assertThat(
                $this->serialize(new \ArrayIterator(['one', 2, 'three'])),
                equals($this->prefixXml('<array><string>one</string><integer>2</integer><string>three</string></array>'))
        );
    }

    /**
     * @test
     */
    public function serializeIndexedIteratorWithGivenTagName()
    {
        assertThat(
                $this->serialize(
                        new \ArrayIterator(['one', 2, 'three']),
                        'root'
                ),
                equals($this->prefixXml('<root><string>one</string><integer>2</integer><string>three</string></root>'))
        );
    }

    /**
     * @test
     */
    public function serializeIndexedIteratorWithGivenTagNameAndElementTagName()
    {
        assertThat(
                $this->serialize(
                        new \ArrayIterator(['one', 2, 'three']),
                        'root',
                        'foo'
                ),
                equals($this->prefixXml('<root><foo>one</foo><foo>2</foo><foo>three</foo></root>'))
        );
    }

    /**
     * @test
     */
    public function serializeNestedIterator()
    {
        assertThat(
                $this->serialize(
                        new \ArrayIterator(
                                ['one'   => 'two',
                                 'three' => new \ArrayIterator(['four' => 'five'])
                                ]
                        ),
                        'root'
                ),
                equals($this->prefixXml('<root><one>two</one><three><four>five</four></three></root>'))
        );
    }

    /**
     * @test
     * @since  4.2.0
     */
    public function serializeAssociativeSequenceWithoutTagName()
    {
        assertThat(
                $this->serialize(Sequence::of(['one' => 'two', 'three' => 'four'])),
                equals($this->prefixXml('<array><one>two</one><three>four</three></array>'))
        );
    }

    /**
     * @test
     * @since  4.2.0
     */
    public function serializeAssociativeSequenceWithGivenTagName()
    {
        assertThat(
                $this->serialize(
                        Sequence::of(
                                ['one'   => 'two',
                                 'three' => 'four'
                                ]
                        ),
                        'root'
                ),
                equals($this->prefixXml('<root><one>two</one><three>four</three></root>'))
        );
    }

    /**
     * @test
     * @since  4.2.0
     */
    public function serializeIndexedSequenceWithoutTagName()
    {
        assertThat(
                $this->serialize(Sequence::of(['one', 2, 'three'])),
                equals($this->prefixXml('<array><string>one</string><integer>2</integer><string>three</string></array>'))
        );
    }

    /**
     * @test
     * @since  4.2.0
     */
    public function serializeIndexedSequenceWithGivenTagName()
    {
        assertThat(
                $this->serialize(Sequence::of(['one', 2, 'three']), 'root'),
                equals($this->prefixXml('<root><string>one</string><integer>2</integer><string>three</string></root>'))
        );
    }

    /**
     * @test
     * @since  4.2.0
     */
    public function serializeIndexedSequenceWithoutTagNameAndGivenElementTagName()
    {
        assertThat(
                $this->serialize(Sequence::of(['one', 2, 'three']), null, 'foo'),
                equals($this->prefixXml('<array><foo>one</foo><foo>2</foo><foo>three</foo></array>'))
        );
    }

    /**
     * @test
     * @since  4.2.0
     */
    public function serializeIndexedSequenceWithGivenTagNameAndElementTagName()
    {
        assertThat(
                $this->serialize(Sequence::of(['one', 2, 'three']), 'root', 'foo'),
                equals($this->prefixXml('<root><foo>one</foo><foo>2</foo><foo>three</foo></root>'))
        );
    }

    /**
     * @test
     * @since  4.2.0
     */
    public function serializeFinalSequence()
    {
        assertThat(
                $this->serialize(
                        Sequence::of(
                            ['one'   => 'two', 'three' => 'four']
                        )->map(function($value) { return strtoupper($value); }),
                        'root'
                ),
                equals($this->prefixXml('<root><one>TWO</one><three>FOUR</three></root>'))
        );
    }

    /**
     * @test
     */
    public function serializeObjectWithoutTagName()
    {
        assertThat(
                $this->serialize(new ExampleObjectClass()),
                equals($this->prefixXml('<foo bar="test"><bar>42</bar></foo>'))
        );
    }

    /**
     * @test
     */
    public function serializeObjectWithGivenTagName()
    {
        assertThat(
                $this->serialize(new ExampleObjectClass(), 'baz'),
                equals($this->prefixXml('<baz bar="test"><bar>42</bar></baz>'))
        );
    }

    /**
     * @test
     */
    public function serializeObjectWithXmlSerializerAnnotation()
    {
        $this->injector->returns(['getInstance' => new ExampleObjectSerializer()]);
        assertThat(
                $this->serialize(new ExampleObjectClassWithSerializer()),
                equals($this->prefixXml('<example sound="303"><anything>something</anything></example>'))
        );
    }

    /**
     * @test
     */
    public function serializeNestedObject()
    {
        $obj      = new ExampleObjectClass();
        $obj->bar = new ExampleObjectClass();
        assertThat(
                $this->serialize($obj),
                equals($this->prefixXml('<foo bar="test"><bar bar="test"><bar>42</bar></bar></foo>'))
        );
    }

    /**
     * @test
     */
    public function serializeObjectWhichContainsArray()
    {
        assertThat(
                $this->serialize(new ContainerWithArrayListTagName()),
                equals($this->prefixXml('<container><list><item>one</item><item>two</item><item>three</item></list></container>'))
        );
    }

    /**
     * @test
     */
    public function serializeObjectWhichContainsArrayWhereArrayTagNameIsDisabled()
    {
        assertThat(
                $this->serialize(new ContainerWithArrayListWithoutTagName()),
                equals($this->prefixXml('<container><item>one</item><item>two</item><item>three</item></container>'))
        );
    }

    /**
     * @test
     */
    public function serializeObjectWhichContainsIterator()
    {
        assertThat(
                $this->serialize(new ContainerWithIterator()),
                equals($this->prefixXml('<container><item>one</item><item>two</item><item>three</item></container>'))
        );
    }

    /**
     * @test
     */
    public function serializeStandardObject()
    {
        assertThat(
                $this->serialize(new ExampleObjectClassWithMethods()),
                equals($this->prefixXml('<class method="returned" isFoo="true" isBar="false"><getBaz>baz</getBaz></class>'))
        );
    }

    /**
     * @test
     */
    public function serializeObjectWithXmlFragment()
    {
        assertThat(
                $this->serialize(new ExampleObjectWithXmlFragments()),
                equals($this->prefixXml('<test><xml><foo>bar</foo></xml><foo>bar</foo><description>foo<br/>' . "\n" . 'b&amp;ar<br/>' . "\n" . '<br/>' . "\n" . 'baz</description></test>'))
        );
    }

    /**
     * @test
     */
    public function serializeObjectWithInvalidXmlFragment()
    {
        assertThat(
                $this->serialize(new ExampleObjectWithInvalidXmlFragments()),
                equals($this->prefixXml('<test><noXml>bar</noXml><noData/></test>'))
        );
    }

    /**
     * @test
     */
    public function serializeObjectWithEmptyAttributes()
    {
        assertThat(
                $this->serialize(new ExampleObjectClassWithEmptyAttributes()),
                equals($this->prefixXml('<test emptyProp2="" emptyMethod2=""/>'))
        );
    }

    /**
     * @test
     */
    public function doesNotSerializeStaticPropertiesAndMethods()
    {
        assertThat(
                $this->serialize(new ExampleStaticClass()),
                equals($this->prefixXml('<ExampleStaticClass/>'))
        );
    }

    /**
     * @test
     */
    public function serializeObjectContainingUmlauts()
    {
        assertThat(
                $this->serialize(new ExampleObjectWithUmlauts()),
                equals($this->prefixXml('<test bar="Hähnchen"><foo>Hähnchen</foo></test>'))
        );
    }

    /**
     * @test
     * @since  4.2.1
     */
    public function serializeObjectOfTraversableWithXmlNonTraversableAnnotation()
    {
        assertThat(
                $this->serialize(new TraversableNonTraversable()),
                equals($this->prefixXml('<TraversableNonTraversable><baz>dummy</baz></TraversableNonTraversable>'))
        );
    }

    /**
     * @test
     * @since  4.2.2
     */
    public function serializeObjectOfTraversableWithXmlTagh()
    {
        assertThat(
                $this->serialize(new TraversableTraversable()),
                equals($this->prefixXml('<foo><example>bar</example></foo>'))
        );
    }

    /**
     * @test
     */
    public function doesNotSerializeResources()
    {
        $fp = fopen(__FILE__, 'rb');
        assertThat($this->serialize($fp), equals('<?xml version="1.0" encoding="UTF-8"?>'));
        fclose($fp);
    }
}
