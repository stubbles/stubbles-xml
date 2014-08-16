<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  stubbles\xml
 */
namespace stubbles\xml\serializer\matcher;
use stubbles\lang\reflect\ReflectionMethod;
use stubbles\lang\reflect\ReflectionProperty;
use stubbles\lang\reflect\matcher\MethodMatcher;
use stubbles\lang\reflect\matcher\PropertyMatcher;
/**
 * Matcher for methods and properties.
 */
class XmlSerializerMethodPropertyMatcher implements MethodMatcher, PropertyMatcher
{
    /**
     * single instance of this matcher
     *
     * @type  \stubbles\xml\serializer\matcher\XmlSerializerMethodPropertyMatcher
     */
    private static $instance;

    /**
     * returns a single instance
     *
     * @return  \stubbles\xml\serializer\matcher\XmlSerializerMethodPropertyMatcher
     * @since   4.0.0
     */
    public static function instance()
    {
        if (null === self::$instance) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * checks whether the matcher is satisfied with the given method
     *
     * @param   \ReflectionMethod  $method
     * @return  bool
     */
    public function matchesMethod(\ReflectionMethod $method)
    {
        if (!$method->isPublic() || $method->isStatic()) {
            return false;
        }

        if ($method->isConstructor() || $method->isDestructor()) {
            return false;
        }

        if (0 == strncmp($method->getName(), '__', 2)) {
            return false;
        }

        if (0 != $method->getNumberOfParameters()) {
            return false;
        }

        return true;
    }

    /**
     * checks whether the matcher is satisfied with the given method
     *
     * @param   \stubbles\lang\reflect\ReflectionMethod  $method
     * @return  bool
     */
    public function matchesAnnotatableMethod(ReflectionMethod $method)
    {
        return ($method->hasAnnotation('XmlIgnore') !== true);
    }

    /**
     * checks whether the matcher is satisfied with the given property
     *
     * @param   \ReflectionProperty  $property
     * @return  bool
     */
    public function matchesProperty(\ReflectionProperty $property)
    {
        if (!$property->isPublic() || $property->isStatic()) {
            return false;
        }

        return true;
    }

    /**
     * checks whether the matcher is satisfied with the given property
     *
     * @param   \stubbles\lang\reflect\ReflectionProperty  $property
     * @return  bool
     */
    public function matchesAnnotatableProperty(ReflectionProperty $property)
    {
        return ($property->hasAnnotation('XmlIgnore') !== true);
    }
}
