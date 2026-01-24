<?php
declare(strict_types=1);
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace stubbles\xml\serializer;

use bovigo\callmap\ClassProxy;
use bovigo\callmap\NewInstance;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use stubbles\helper\serializer\annotationbased\ContainerWithArrayListTagName;
use stubbles\helper\serializer\annotationbased\ContainerWithArrayListWithoutTagName;
use stubbles\helper\serializer\annotationbased\ContainerWithIterator;
use stubbles\helper\serializer\annotationbased\ExampleObjectClass;
use stubbles\helper\serializer\annotationbased\ExampleObjectClassWithEmptyAttributes;
use stubbles\helper\serializer\annotationbased\ExampleObjectClassWithMethods;
use stubbles\helper\serializer\annotationbased\ExampleObjectClassWithSerializer;
use stubbles\helper\serializer\annotationbased\ExampleObjectSerializer;
use stubbles\helper\serializer\annotationbased\ExampleObjectWithInvalidXmlFragments;
use stubbles\helper\serializer\annotationbased\ExampleObjectWithUmlauts;
use stubbles\helper\serializer\annotationbased\ExampleObjectWithXmlFragments;
use stubbles\helper\serializer\annotationbased\ExampleStaticClass;
use stubbles\helper\serializer\annotationbased\TraversableNonTraversable;
use stubbles\helper\serializer\annotationbased\TraversableTraversable;
use stubbles\ioc\Injector;
use stubbles\xml\DomXmlStreamWriter;

use function bovigo\assert\assertThat;
use function bovigo\assert\predicate\equals;
/**
 * Test for stubbles\xml\serializer\XmlSerializer.
 *
 * @deprecated since 10.1
 */
#[Group('xml')]
#[Group('xml_serializer')]
class XmlSerializerAnnotationsTest extends TestCase
{
    private XmlSerializer $serializer;
    private Injector&ClassProxy $injector;

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
    private function serialize(
        mixed $value,
        ?string $tagName = null,
        ?string $elementTagName = null
    ): string {
        return $this->serializer->serialize(
            $value,
            new DomXmlStreamWriter(),
            $tagName,
            $elementTagName
        )->asXml();
    }

    private function prefixXml(string $xml): string
    {
        return '<?xml version="1.0" encoding="UTF-8"?>' . "\n" . $xml;
    }

    #[Test]
    public function serializeObjectWithoutTagName(): void
    {
        assertThat(
            $this->serialize(new ExampleObjectClass()),
            equals($this->prefixXml('<foo bar="test"><bar>42</bar></foo>'))
        );
    }

    #[Test]
    public function serializeObjectWithGivenTagName(): void
    {
        assertThat(
            $this->serialize(new ExampleObjectClass(), 'baz'),
            equals($this->prefixXml('<baz bar="test"><bar>42</bar></baz>'))
        );
    }

    #[Test]
    public function serializeObjectWithXmlSerializerAnnotation(): void
    {
        $this->injector->returns(['getInstance' => new ExampleObjectSerializer()]);
        assertThat(
            $this->serialize(new ExampleObjectClassWithSerializer()),
            equals($this->prefixXml('<example sound="303"><anything>something</anything></example>'))
        );
    }

    #[Test]
    public function serializeNestedObject(): void
    {
        $obj      = new ExampleObjectClass();
        $obj->bar = new ExampleObjectClass();
        assertThat(
            $this->serialize($obj),
            equals($this->prefixXml('<foo bar="test"><bar bar="test"><bar>42</bar></bar></foo>'))
        );
    }

    #[Test]
    public function serializeObjectWhichContainsArray(): void
    {
        assertThat(
            $this->serialize(new ContainerWithArrayListTagName()),
            equals($this->prefixXml('<container><list><item>one</item><item>two</item><item>three</item></list></container>'))
        );
    }

    #[Test]
    public function serializeObjectWhichContainsArrayWhereArrayTagNameIsDisabled(): void
    {
        assertThat(
            $this->serialize(new ContainerWithArrayListWithoutTagName()),
            equals($this->prefixXml('<container><item>one</item><item>two</item><item>three</item></container>'))
        );
    }

    #[Test]
    public function serializeObjectWhichContainsIterator(): void
    {
        assertThat(
            $this->serialize(new ContainerWithIterator()),
            equals($this->prefixXml('<container><item>one</item><item>two</item><item>three</item></container>'))
        );
    }

    #[Test]
    public function serializeStandardObject(): void
    {
        assertThat(
            $this->serialize(new ExampleObjectClassWithMethods()),
            equals($this->prefixXml('<class method="returned" isFoo="true" isBar="false"><getBaz>baz</getBaz></class>'))
        );
    }

    #[Test]
    public function serializeObjectWithXmlFragment(): void
    {
        assertThat(
            $this->serialize(new ExampleObjectWithXmlFragments()),
            equals($this->prefixXml('<test><xml><foo>bar</foo></xml><foo>bar</foo><description>foo<br/>' . "\n" . 'b&amp;ar<br/>' . "\n" . '<br/>' . "\n" . 'baz</description></test>'))
        );
    }

    #[Test]
    public function serializeObjectWithInvalidXmlFragment(): void
    {
        assertThat(
            $this->serialize(new ExampleObjectWithInvalidXmlFragments()),
            equals($this->prefixXml('<test><noXml>bar</noXml><noData/></test>'))
        );
    }

    #[Test]
    public function serializeObjectWithEmptyAttributes(): void
    {
        assertThat(
            $this->serialize(new ExampleObjectClassWithEmptyAttributes()),
            equals($this->prefixXml('<test emptyProp2="" emptyMethod2=""/>'))
        );
    }

    #[Test]
    public function doesNotSerializeStaticPropertiesAndMethods(): void
    {
        assertThat(
            $this->serialize(new ExampleStaticClass()),
            equals($this->prefixXml('<ExampleStaticClass/>'))
        );
    }

    #[Test]
    public function serializeObjectContainingUmlauts(): void
    {
        assertThat(
            $this->serialize(new ExampleObjectWithUmlauts()),
            equals($this->prefixXml('<test bar="Hähnchen"><foo>Hähnchen</foo></test>'))
        );
    }

    /**
     * @since  4.2.1
     */
    #[Test]
    public function serializeObjectOfTraversableWithXmlNonTraversableAnnotation(): void
    {
        assertThat(
            $this->serialize(new TraversableNonTraversable()),
            equals($this->prefixXml('<TraversableNonTraversable><baz>dummy</baz></TraversableNonTraversable>'))
        );
    }

    /**
     * @since  4.2.2
     */
    #[Test]
    public function serializeObjectOfTraversableWithXmlTagh(): void
    {
        assertThat(
            $this->serialize(new TraversableTraversable()),
            equals($this->prefixXml('<foo><example>bar</example></foo>'))
        );
    }
}
