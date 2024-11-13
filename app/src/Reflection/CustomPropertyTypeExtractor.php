<?php

namespace App\Reflection;

use Symfony\Component\PropertyInfo\PropertyTypeExtractorInterface;
use Symfony\Component\PropertyInfo\Type;

/**
 * This PropertyTypeExtractor is based on methods and logics of the ReflectionExtractor.
 *
 * @see \Symfony\Component\PropertyInfo\Extractor\ReflectionExtractor
 */
class CustomPropertyTypeExtractor implements PropertyTypeExtractorInterface
{
    private const MAP_TYPES = [
        'integer' => Type::BUILTIN_TYPE_INT,
        'boolean' => Type::BUILTIN_TYPE_BOOL,
        'double' => Type::BUILTIN_TYPE_FLOAT,
    ];

    public function getTypes(string $class, string $property, array $context = []): ?array
    {
        try {
            $reflectionClass = new \ReflectionClass($class);

            $reflectionProperty = $reflectionClass->getProperty($property);
            $reflectionPropertyType = $reflectionProperty->getType();

            if (null !== $reflectionPropertyType && $types = $this->extractFromReflectionType($reflectionPropertyType, $reflectionProperty->getDeclaringClass())) {
                return $types;
            }

        } catch (\ReflectionException $e) {
            // Do nothing
        }

        return null;
    }

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

            if ($phpTypeOrClass === 'mixed') {
                continue;
            }

            $phpTypeOrClass = static::MAP_TYPES[$phpTypeOrClass] ?? $phpTypeOrClass;

            if (Type::BUILTIN_TYPE_ARRAY === $phpTypeOrClass) {
                $types[] = new Type(Type::BUILTIN_TYPE_ARRAY, $nullable, null, true);
            } elseif ($type->isBuiltin()) {
                $types[] = new Type($phpTypeOrClass, $nullable);
            } else {
                $types[] = new Type(Type::BUILTIN_TYPE_OBJECT, $nullable, $phpTypeOrClass);
            }
        }

        return $types;
    }
}
