<?php declare(strict_types = 1);

namespace App\Normalizer;

use App\Normalizer\Value\BooleanValue;
use App\Normalizer\Value\StringValue;
use App\Normalizer\Value\ValueInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Webmozart\Assert\Assert;

class MappingTableNormalizer implements NormalizerInterface, DenormalizerInterface
{
    public const TABLE = 'mapping_table';

    private const SUPPORTED_TYPES = [
        StringValue::class,
        BooleanValue::class
    ];

    public function normalize(mixed $object, string $format = null, array $context = []): ?string
    {
        if (null === $object?->getValue() || '' === $object?->getValue()) {
            return ''; // Reset value from property in Emarsys CRM
        }

        Assert::keyExists($context, self::TABLE, sprintf('MappingTable not set (%s)', \get_class($object)));

        $key = array_search($object->getValue(), $context[self::TABLE], true);
        return $key ? (string)$key : null;
    }

    public function supportsNormalization(mixed $data, string $format = null): bool
    {
        return $data instanceof ValueInterface;
    }

    public function denormalize(mixed $data, string $type, ?string $format = null, array $context = []): ?ValueInterface
    {
        Assert::keyExists($context, self::TABLE, sprintf('MappingTable not set (%s)', $type));
        $mappingTable = $context[self::TABLE];

        return isset($mappingTable[$data]) ? new $type($mappingTable[$data]) : new $type(null);
    }

    public function supportsDenormalization(mixed $data, string $type, ?string $format = null): bool
    {
        return in_array($type, self::SUPPORTED_TYPES);
    }

    public function getSupportedTypes(?string $format): array
    {
        return [
            '*' => false
        ];
    }
}
