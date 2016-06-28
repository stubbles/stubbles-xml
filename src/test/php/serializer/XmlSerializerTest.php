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
require_once __DIR__ . '/examples/TraversableNonTraversable.php';
require_once __DIR__ . '/examples/TraversableTraversable.php';
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
use org\stubbles\test\xml\serializer\TraversableNonTraversable;
use org\stubbles\test\xml\serializer\TraversableTraversable;
use stubbles\ioc\Injector;
use stubbles\sequence\Sequence;
use stubbles\xml\DomXmlStreamWriter;

use function bovigo\assert\assert;
use function bovigo\assert\expect;
use function bovigo\assert\predicate\equals;
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
        $this->injector   = NewInstance::stub(Injector::class);
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
        assert(
                $this->serialize(null),
                equals($this->prefixXml('<null><null/></null>'))
        );
    }

    /**
     * @test
     */
    public function serializeNullWithGivenTagName()
    {
        assert(
                $this->serialize(null, 'root'),
                equals($this->prefixXml('<root><null/></root>'))
        );
    }

    /**
     * @test
     */
    public function serializeBooleanTrueWithoutTagName()
    {
        assert(
                $this->serialize(true),
                equals($this->prefixXml('<boolean>true</boolean>'))
        );
    }

    /**
     * @test
     */
    public function serializeBooleanTrueWithGivenTagName()
    {
        assert(
                $this->serialize(true, 'root'),
                equals($this->prefixXml('<root>true</root>'))
        );
    }

    /**
     * @test
     */
    public function serializeBooleanFalseWithoutTagName()
    {
        assert(
                $this->serialize(false),
                equals($this->prefixXml('<boolean>false</boolean>'))
        );
    }

    /**
     * @test
     */
    public function serializeBooleanFalseWithGivenTagName()
    {
        assert(
                $this->serialize(false, 'root'),
                equals($this->prefixXml('<root>false</root>'))
        );
    }

    /**
     * @test
     */
    public function serializeStringWithoutTagName()
    {
        assert(
                $this->serialize('This is a string.'),
                equals($this->prefixXml('<string>This is a string.</string>'))
        );
    }

    /**
     * @test
     */
    public function serializeStringWithGivenTagName()
    {
        assert(
                $this->serialize('This is a string.', 'root'),
                equals($this->prefixXml('<root>This is a string.</root>'))
        );
    }

    /**
     * @test
     */
    public function serializeStringWithoutTagNameDirectly()
    {
        assert(
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
        assert(
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
        assert(
                $this->serialize(45),
                equals($this->prefixXml('<integer>45</integer>'))
        );
    }

    /**
     * @test
     */
    public function serializeIntegerWithGivenTagName()
    {
        assert(
                $this->serialize(45, 'root'),
                equals($this->prefixXml('<root>45</root>'))
        );
    }

    /**
     * @test
     */
    public function serializeIntegerWithoutTagNameDirectly()
    {
        assert(
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
        assert(
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
        assert(
                $this->serialize(2.352),
                equals($this->prefixXml('<double>2.352</double>'))
        );
    }

    /**
     * @test
     */
    public function serializeFloatWithGivenTagName()
    {
        assert(
                $this->serialize(2.352, 'root'),
                equals($this->prefixXml('<root>2.352</root>'))
        );
    }

    /**
     * @test
     */
    public function serializeFloatWithoutTagNameDirectly()
    {
        assert(
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
        assert(
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
        assert(
                $this->serialize(['one' => 'two', 'three' => 'four']),
                equals($this->prefixXml('<array><one>two</one><three>four</three></array>'))
        );
    }

    /**
     * @test
     */
    public function serializeAssociativeArrayWithGivenTagName()
    {
        assert(
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
        assert(
                $this->serialize(['one', 2, 'three']),
                equals($this->prefixXml('<array><string>one</string><integer>2</integer><string>three</string></array>'))
        );
    }

    /**
     * @test
     */
    public function serializeIndexedArrayWithGivenTagName()
    {
        assert(
                $this->serialize(['one', 2, 'three'], 'root'),
                equals($this->prefixXml('<root><string>one</string><integer>2</integer><string>three</string></root>'))
        );
    }

    /**
     * @test
     */
    public function serializeIndexedArrayWithoutTagNameAndGivenElementTagName()
    {
        assert(
                $this->serialize(['one', 2, 'three'], null, 'foo'),
                equals($this->prefixXml('<array><foo>one</foo><foo>2</foo><foo>three</foo></array>'))
        );
    }

    /**
     * @test
     */
    public function serializeIndexedArrayWithGivenTagNameAndElementTagName()
    {
        assert(
                $this->serialize(['one', 2, 'three'], 'root', 'foo'),
                equals($this->prefixXml('<root><foo>one</foo><foo>2</foo><foo>three</foo></root>'))
        );
    }

    /**
     * @test
     */
    public function serializeNestedArray()
    {
        assert(
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
        assert(
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
        assert(
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
        assert(
                $this->serialize(new \ArrayIterator(['one', 2, 'three'])),
                equals($this->prefixXml('<array><string>one</string><integer>2</integer><string>three</string></array>'))
        );
    }

    /**
     * @test
     */
    public function serializeIndexedIteratorWithGivenTagName()
    {
        assert(
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
        assert(
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
        assert(
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
        assert(
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
        assert(
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
        assert(
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
        assert(
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
        assert(
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
        assert(
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
        assert(
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
        assert(
                $this->serialize(new ExampleObjectClass()),
                equals($this->prefixXml('<foo bar="test"><bar>42</bar></foo>'))
        );
    }

    /**
     * @test
     */
    public function serializeObjectWithGivenTagName()
    {
        assert(
                $this->serialize(new ExampleObjectClass(), 'baz'),
                equals($this->prefixXml('<baz bar="test"><bar>42</bar></baz>'))
        );
    }

    /**
     * @test
     */
    public function serializeObjectWithXmlSerializerAnnotation()
    {
        $this->injector->mapCalls(['getInstance' => new ExampleObjectSerializer()]);
        assert(
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
        assert(
                $this->serialize($obj),
                equals($this->prefixXml('<foo bar="test"><bar bar="test"><bar>42</bar></bar></foo>'))
        );
    }

    /**
     * @test
     */
    public function serializeObjectWhichContainsArray()
    {
        assert(
                $this->serialize(new ContainerWithArrayListTagName()),
                equals($this->prefixXml('<container><list><item>one</item><item>two</item><item>three</item></list></container>'))
        );
    }

    /**
     * @test
     */
    public function serializeObjectWhichContainsArrayWhereArrayTagNameIsDisabled()
    {
        assert(
                $this->serialize(new ContainerWithArrayListWithoutTagName()),
                equals($this->prefixXml('<container><item>one</item><item>two</item><item>three</item></container>'))
        );
    }

    /**
     * @test
     */
    public function serializeObjectWhichContainsIterator()
    {
        assert(
                $this->serialize(new ContainerWithIterator()),
                equals($this->prefixXml('<container><item>one</item><item>two</item><item>three</item></container>'))
        );
    }

    /**
     * @test
     */
    public function serializeStandardObject()
    {
        assert(
                $this->serialize(new ExampleObjectClassWithMethods()),
                equals($this->prefixXml('<class method="returned" isFoo="true" isBar="false"><getBaz>baz</getBaz></class>'))
        );
    }

    /**
     * @test
     */
    public function serializeObjectWithXmlFragment()
    {
        assert(
                $this->serialize(new ExampleObjectWithXmlFragments()),
                equals($this->prefixXml('<test><xml><foo>bar</foo></xml><foo>bar</foo><description>foo<br/>' . "\n" . 'b&amp;ar<br/>' . "\n" . '<br/>' . "\n" . 'baz</description></test>'))
        );
    }

    /**
     * @test
     */
    public function serializeObjectWithInvalidXmlFragment()
    {
        assert(
                $this->serialize(new ExampleObjectWithInvalidXmlFragments()),
                equals($this->prefixXml('<test><noXml>bar</noXml><noData/></test>'))
        );
    }

    /**
     * @test
     */
    public function serializeObjectWithEmptyAttributes()
    {
        assert(
                $this->serialize(new ExampleObjectClassWithEmptyAttributes()),
                equals($this->prefixXml('<test emptyProp2="" emptyMethod2=""/>'))
        );
    }

    /**
     * @test
     */
    public function doesNotSerializeStaticPropertiesAndMethods()
    {
        assert(
                $this->serialize(new ExampleStaticClass()),
                equals($this->prefixXml('<ExampleStaticClass/>'))
        );
    }

    /**
     * @test
     */
    public function serializeObjectContainingUmlauts()
    {
        assert(
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
        assert(
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
        assert(
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
        assert($this->serialize($fp), equals('<?xml version="1.0" encoding="UTF-8"?>'));
        fclose($fp);
    }
}
