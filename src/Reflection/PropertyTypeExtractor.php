<?php

namespace App\Reflection;

use Symfony\Component\PropertyInfo\PropertyTypeExtractorInterface;
use Symfony\Component\PropertyInfo\Type;

/**
 * This PropertyTypeExtractor is created from methods of the ReflectionExtractor
 *
 * @see \Symfony\Component\PropertyInfo\Extractor\ReflectionExtractor
 */
class PropertyTypeExtractor implements PropertyTypeExtractorInterface
{
    /**
     * @see \Symfony\Component\PropertyInfo\Extractor\ReflectionExtractor::MAP_TYPES
     */
    private const MAP_TYPES = [
        'integer' => Type::BUILTIN_TYPE_INT,
        'boolean' => Type::BUILTIN_TYPE_BOOL,
        'double' => Type::BUILTIN_TYPE_FLOAT,
    ];

    /**
     * @see \Symfony\Component\PropertyInfo\Extractor\ReflectionExtractor::extractFromPropertyDeclaration()
     */
    public function getTypes(string $class, string $property, array $context = []): ?array
    {
        try {
            $reflectionClass = new \ReflectionClass($class);

            if (\PHP_VERSION_ID >= 70400) {
                $reflectionProperty = $reflectionClass->getProperty($property);
                $reflectionPropertyType = $reflectionProperty->getType();

                if (null !== $reflectionPropertyType && $types = $this->extractFromReflectionType($reflectionPropertyType, $reflectionProperty->getDeclaringClass())) {
                    return $types;
                }
            }
        } catch (\ReflectionException $e) {
            return null;
        }

        $defaultValue = $reflectionClass->getDefaultProperties()[$property] ?? null;

        if (null === $defaultValue) {
            return null;
        }

        $type = \gettype($defaultValue);
        $type = static::MAP_TYPES[$type] ?? $type;

        return [new Type($type, $this->isNullableProperty($class, $property), null, Type::BUILTIN_TYPE_ARRAY === $type)];
    }

    /**
     * @see \Symfony\Component\PropertyInfo\Extractor\ReflectionExtractor::extractFromReflectionType()
     */
    private function extractFromReflectionType(\ReflectionType $reflectionType, \ReflectionClass $declaringClass): array
    {
        $types = [];
        $nullable = $reflectionType->allowsNull();

        foreach (($reflectionType instanceof \ReflectionUnionType || $reflectionType instanceof \ReflectionIntersectionType) ? $reflectionType->getTypes() : [$reflectionType] as $type) {
            if (!$type instanceof \ReflectionNamedType) {
                // Nested composite types are not supported yet.
                return [];
            }

            $phpTypeOrClass = $type->getName();
            if ('null' === $phpTypeOrClass || 'mixed' === $phpTypeOrClass || 'never' === $phpTypeOrClass) {
                continue;
            }

            if (Type::BUILTIN_TYPE_ARRAY === $phpTypeOrClass) {
                $types[] = new Type(Type::BUILTIN_TYPE_ARRAY, $nullable, null, true);
            } elseif ('void' === $phpTypeOrClass) {
                $types[] = new Type(Type::BUILTIN_TYPE_NULL, $nullable);
            } elseif ($type->isBuiltin()) {
                $types[] = new Type($phpTypeOrClass, $nullable);
            } else {
                $types[] = new Type(Type::BUILTIN_TYPE_OBJECT, $nullable, $this->resolveTypeName($phpTypeOrClass, $declaringClass));
            }
        }

        return $types;
    }

    /**
     * @see \Symfony\Component\PropertyInfo\Extractor\ReflectionExtractor::resolveTypeName()
     */
    private function resolveTypeName(string $name, \ReflectionClass $declaringClass): string
    {
        if ('self' === $lcName = strtolower($name)) {
            return $declaringClass->name;
        }
        if ('parent' === $lcName && $parent = $declaringClass->getParentClass()) {
            return $parent->name;
        }

        return $name;
    }

    /**
     * Taken from ReflectionExtractor::isNullableProperty()
     *
     * @see \Symfony\Component\PropertyInfo\Extractor\ReflectionExtractor::isNullableProperty()
     */
    private function isNullableProperty(string $class, string $property): bool
    {
        try {
            $reflectionProperty = new \ReflectionProperty($class, $property);

            if (\PHP_VERSION_ID >= 70400) {
                $reflectionPropertyType = $reflectionProperty->getType();

                return null !== $reflectionPropertyType && $reflectionPropertyType->allowsNull();
            }

            return false;
        } catch (\ReflectionException $e) {
            // Return false if the property doesn't exist
        }

        return false;
    }
}
