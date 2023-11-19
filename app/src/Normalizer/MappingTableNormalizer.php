<?php declare(strict_types = 1);

namespace App\Normalizer;

use App\Normalizer\Value\BooleanValue;
use App\Normalizer\Value\StringValue;
use App\Normalizer\Value\ValueInterface;
use InvalidArgumentException;
use Symfony\Component\Serializer\Normalizer\CacheableSupportsMethodInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class MappingTableNormalizer implements NormalizerInterface, DenormalizerInterface, CacheableSupportsMethodInterface
{
    public const TABLE = 'mapping_table';

    private const SUPPORTED_TYPES = [
        StringValue::class,
        BooleanValue::class
    ];

    public function normalize(mixed $object, string $format = null, array $context = []): ?string
    {
        if (null === $object?->getValue() || '' === $object?->getValue()) {
            return ''; // Reset value in CRM
        }

        $mappingTable = $this->getMappingTable($context);

        $key = array_search($object->getValue(), $mappingTable);
        if($key) {
            return (string)$key; // Force string
        }
        return null;
    }

    public function supportsNormalization(mixed $data, string $format = null): bool
    {
        return $data instanceof ValueInterface;
    }

    public function denormalize($data, $type, $format = null, array $context = array()): mixed
    {
        $mappingTable = $this->getMappingTable($context);

        foreach ($mappingTable as $key => $value) {
            if ((string)$key === $data) {
                return new $type($value);
            }
        }

        return new $type(null);
    }

    public function supportsDenormalization($data, $type, $format = null): bool
    {
        return in_array($type, self::SUPPORTED_TYPES);
    }

    private function getMappingTable(array $context): array
    {
        if (!isset($context[self::TABLE]) || !is_array($context[self::TABLE])) {
            throw new InvalidArgumentException('mapping_table not defined');
        }

        return $context[self::TABLE];
    }

    public function hasCacheableSupportsMethod(): bool
    {
        return __CLASS__ === static::class;
    }
}
