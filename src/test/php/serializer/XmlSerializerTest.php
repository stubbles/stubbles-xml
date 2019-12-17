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
use function bovigo\assert\fail;
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
     * @var XmlSerializer
     */
    private $serializer;
    /**
     * mocked injector instance
     *
     * @var  Injector&\bovigo\callmap\ClassProxy
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

    /**
     * @param   mixed  $value
     * @param   string  $tagName
     * @param   string  $elementTagName
     * @return  string
     */
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
    public function serializeNullWithoutTagName(): void
    {
        assertThat(
                $this->serialize(null),
                equals($this->prefixXml('<null><null/></null>'))
        );
    }

    /**
     * @test
     */
    public function serializeNullWithGivenTagName(): void
    {
        assertThat(
                $this->serialize(null, 'root'),
                equals($this->prefixXml('<root><null/></root>'))
        );
    }

    /**
     * @test
     */
    public function serializeBooleanTrueWithoutTagName(): void
    {
        assertThat(
                $this->serialize(true),
                equals($this->prefixXml('<boolean>true</boolean>'))
        );
    }

    /**
     * @test
     */
    public function serializeBooleanTrueWithGivenTagName(): void
    {
        assertThat(
                $this->serialize(true, 'root'),
                equals($this->prefixXml('<root>true</root>'))
        );
    }

    /**
     * @test
     */
    public function serializeBooleanFalseWithoutTagName(): void
    {
        assertThat(
                $this->serialize(false),
                equals($this->prefixXml('<boolean>false</boolean>'))
        );
    }

    /**
     * @test
     */
    public function serializeBooleanFalseWithGivenTagName(): void
    {
        assertThat(
                $this->serialize(false, 'root'),
                equals($this->prefixXml('<root>false</root>'))
        );
    }

    /**
     * @test
     */
    public function serializeStringWithoutTagName(): void
    {
        assertThat(
                $this->serialize('This is a string.'),
                equals($this->prefixXml('<string>This is a string.</string>'))
        );
    }

    /**
     * @test
     */
    public function serializeStringWithGivenTagName(): void
    {
        assertThat(
                $this->serialize('This is a string.', 'root'),
                equals($this->prefixXml('<root>This is a string.</root>'))
        );
    }

    /**
     * @test
     */
    public function serializeStringWithoutTagNameDirectly(): void
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
    public function serializeStringWithGivenTagNameDirectly(): void
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
    public function serializeIntegerWithoutTagName(): void
    {
        assertThat(
                $this->serialize(45),
                equals($this->prefixXml('<integer>45</integer>'))
        );
    }

    /**
     * @test
     */
    public function serializeIntegerWithGivenTagName(): void
    {
        assertThat(
                $this->serialize(45, 'root'),
                equals($this->prefixXml('<root>45</root>'))
        );
    }

    /**
     * @test
     */
    public function serializeIntegerWithoutTagNameDirectly(): void
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
    public function serializeIntegerWithGivenTagNameDirectly(): void
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
    public function serializeFloatWithoutTagName(): void
    {
        assertThat(
                $this->serialize(2.352),
                equals($this->prefixXml('<double>2.352</double>'))
        );
    }

    /**
     * @test
     */
    public function serializeFloatWithGivenTagName(): void
    {
        assertThat(
                $this->serialize(2.352, 'root'),
                equals($this->prefixXml('<root>2.352</root>'))
        );
    }

    /**
     * @test
     */
    public function serializeFloatWithoutTagNameDirectly(): void
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
    public function serializeFloatWithGivenTagNameDirectly(): void
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
    public function serializeAssociativeArrayWithoutTagName(): void
    {
        assertThat(
                $this->serialize(['one' => 'two', 'three' => 'four']),
                equals($this->prefixXml('<array><one>two</one><three>four</three></array>'))
        );
    }

    /**
     * @test
     */
    public function serializeAssociativeArrayWithGivenTagName(): void
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
    public function serializeIndexedArrayWithoutTagName(): void
    {
        assertThat(
                $this->serialize(['one', 2, 'three']),
                equals($this->prefixXml('<array><string>one</string><integer>2</integer><string>three</string></array>'))
        );
    }

    /**
     * @test
     */
    public function serializeIndexedArrayWithGivenTagName(): void
    {
        assertThat(
                $this->serialize(['one', 2, 'three'], 'root'),
                equals($this->prefixXml('<root><string>one</string><integer>2</integer><string>three</string></root>'))
        );
    }

    /**
     * @test
     */
    public function serializeIndexedArrayWithoutTagNameAndGivenElementTagName(): void
    {
        assertThat(
                $this->serialize(['one', 2, 'three'], null, 'foo'),
                equals($this->prefixXml('<array><foo>one</foo><foo>2</foo><foo>three</foo></array>'))
        );
    }

    /**
     * @test
     */
    public function serializeIndexedArrayWithGivenTagNameAndElementTagName(): void
    {
        assertThat(
                $this->serialize(['one', 2, 'three'], 'root', 'foo'),
                equals($this->prefixXml('<root><foo>one</foo><foo>2</foo><foo>three</foo></root>'))
        );
    }

    /**
     * @test
     */
    public function serializeNestedArray(): void
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
    public function serializeAssociativeIteratorWithoutTagName(): void
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
    public function serializeAssociativeIteratorWithGivenTagName(): void
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
    public function serializeIndexedIteratorWithoutTagName(): void
    {
        assertThat(
                $this->serialize(new \ArrayIterator(['one', 2, 'three'])),
                equals($this->prefixXml('<array><string>one</string><integer>2</integer><string>three</string></array>'))
        );
    }

    /**
     * @test
     */
    public function serializeIndexedIteratorWithGivenTagName(): void
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
    public function serializeIndexedIteratorWithGivenTagNameAndElementTagName(): void
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
    public function serializeNestedIterator(): void
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
    public function serializeAssociativeSequenceWithoutTagName(): void
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
    public function serializeAssociativeSequenceWithGivenTagName(): void
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
    public function serializeIndexedSequenceWithoutTagName(): void
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
    public function serializeIndexedSequenceWithGivenTagName(): void
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
    public function serializeIndexedSequenceWithoutTagNameAndGivenElementTagName(): void
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
    public function serializeIndexedSequenceWithGivenTagNameAndElementTagName(): void
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
    public function serializeFinalSequence(): void
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
    public function serializeObjectWithoutTagName(): void
    {
        assertThat(
                $this->serialize(new ExampleObjectClass()),
                equals($this->prefixXml('<foo bar="test"><bar>42</bar></foo>'))
        );
    }

    /**
     * @test
     */
    public function serializeObjectWithGivenTagName(): void
    {
        assertThat(
                $this->serialize(new ExampleObjectClass(), 'baz'),
                equals($this->prefixXml('<baz bar="test"><bar>42</bar></baz>'))
        );
    }

    /**
     * @test
     */
    public function serializeObjectWithXmlSerializerAnnotation(): void
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
    public function serializeNestedObject(): void
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
    public function serializeObjectWhichContainsArray(): void
    {
        assertThat(
                $this->serialize(new ContainerWithArrayListTagName()),
                equals($this->prefixXml('<container><list><item>one</item><item>two</item><item>three</item></list></container>'))
        );
    }

    /**
     * @test
     */
    public function serializeObjectWhichContainsArrayWhereArrayTagNameIsDisabled(): void
    {
        assertThat(
                $this->serialize(new ContainerWithArrayListWithoutTagName()),
                equals($this->prefixXml('<container><item>one</item><item>two</item><item>three</item></container>'))
        );
    }

    /**
     * @test
     */
    public function serializeObjectWhichContainsIterator(): void
    {
        assertThat(
                $this->serialize(new ContainerWithIterator()),
                equals($this->prefixXml('<container><item>one</item><item>two</item><item>three</item></container>'))
        );
    }

    /**
     * @test
     */
    public function serializeStandardObject(): void
    {
        assertThat(
                $this->serialize(new ExampleObjectClassWithMethods()),
                equals($this->prefixXml('<class method="returned" isFoo="true" isBar="false"><getBaz>baz</getBaz></class>'))
        );
    }

    /**
     * @test
     */
    public function serializeObjectWithXmlFragment(): void
    {
        assertThat(
                $this->serialize(new ExampleObjectWithXmlFragments()),
                equals($this->prefixXml('<test><xml><foo>bar</foo></xml><foo>bar</foo><description>foo<br/>' . "\n" . 'b&amp;ar<br/>' . "\n" . '<br/>' . "\n" . 'baz</description></test>'))
        );
    }

    /**
     * @test
     */
    public function serializeObjectWithInvalidXmlFragment(): void
    {
        assertThat(
                $this->serialize(new ExampleObjectWithInvalidXmlFragments()),
                equals($this->prefixXml('<test><noXml>bar</noXml><noData/></test>'))
        );
    }

    /**
     * @test
     */
    public function serializeObjectWithEmptyAttributes(): void
    {
        assertThat(
                $this->serialize(new ExampleObjectClassWithEmptyAttributes()),
                equals($this->prefixXml('<test emptyProp2="" emptyMethod2=""/>'))
        );
    }

    /**
     * @test
     */
    public function doesNotSerializeStaticPropertiesAndMethods(): void
    {
        assertThat(
                $this->serialize(new ExampleStaticClass()),
                equals($this->prefixXml('<ExampleStaticClass/>'))
        );
    }

    /**
     * @test
     */
    public function serializeObjectContainingUmlauts(): void
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
    public function serializeObjectOfTraversableWithXmlNonTraversableAnnotation(): void
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
    public function serializeObjectOfTraversableWithXmlTagh(): void
    {
        assertThat(
                $this->serialize(new TraversableTraversable()),
                equals($this->prefixXml('<foo><example>bar</example></foo>'))
        );
    }

    /**
     * @test
     */
    public function doesNotSerializeResources(): void
    {
        $fp = fopen(__FILE__, 'rb');
        if (false === $fp) {
            fail('Could not open file for test');
        }

        assertThat($this->serialize($fp), equals('<?xml version="1.0" encoding="UTF-8"?>'));
        fclose($fp);
    }
}
