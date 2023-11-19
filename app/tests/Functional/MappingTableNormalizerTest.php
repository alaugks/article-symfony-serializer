<?php declare(strict_types = 1);

namespace App\Tests\Functional;

use App\Normalizer\MappingTableNormalizer;
use App\Normalizer\Value\StringValue;
use InvalidArgumentException;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class MappingTableNormalizerTest extends KernelTestCase
{
    public array $emptyMappingTable = [];

    public array $mappingTable = ['1' => 'MALE', '2' => 'FEMALE', '6' => 'DIVERS'];

    private MappingTableNormalizer $mappingTableNormalizer;

    public function setUp(): void
    {
        $this->mappingTableNormalizer = new MappingTableNormalizer();
    }

    public function testMappingTableNotDefined()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->mappingTableNormalizer->normalize(
            new StringValue('FEMALE'),
            'json'
        );
    }

    public function testSupportsNormalization()
    {
        $this->assertTrue(
            $this->mappingTableNormalizer->supportsNormalization(
                new StringValue('FEMALE')
            )
        );

        $this->assertFalse(
            $this->mappingTableNormalizer->supportsNormalization('FEMALE')
        );
    }

    public function testNormalizationMapped()
    {
        $this->assertSame(
            '2',
            $this->mappingTableNormalizer->normalize(
                new StringValue('FEMALE'),
                'json',
                [MappingTableNormalizer::TABLE => $this->mappingTable]
            )
        );
    }

    public function testNormalizationNoMapped()
    {
        $this->assertNull(
            $this->mappingTableNormalizer->normalize(
                new StringValue('FOO'),
                'json',
                [MappingTableNormalizer::TABLE => $this->mappingTable]
            )
        );
    }

    public function testNormalizationToEmptyString()
    {
        $this->assertSame(
            '',
            $this->mappingTableNormalizer->normalize(
                null,
                'json',
                [MappingTableNormalizer::TABLE => $this->mappingTable]
            )
        );
    }

    public function testSupportsDenormalization()
    {
        $this->assertTrue(
            $this->mappingTableNormalizer->supportsDenormalization(
                '1',
                StringValue::class
            )
        );

        $this->assertFalse(
            $this->mappingTableNormalizer->supportsDenormalization(
                '1',
                'string'
            )
        );
    }

    public function testDenormalizationMapped()
    {
        $this->assertSame(
            'FEMALE',
            $this->mappingTableNormalizer->denormalize(
                '2',
                StringValue::class,
                'json',
                [MappingTableNormalizer::TABLE => $this->mappingTable]
            )->getValue()
        );
    }

    public function testDenormalizationNoMapped()
    {
        $this->assertNull(
            $this->mappingTableNormalizer->denormalize(
                '7',
                StringValue::class,
                'json',
                [MappingTableNormalizer::TABLE => $this->mappingTable]
            )->getValue()
        );
    }

    public function testDenormalizationToEmptyString()
    {
        $this->assertNull(
            $this->mappingTableNormalizer->denormalize(
                null,
                StringValue::class,
                'json',
                [MappingTableNormalizer::TABLE => $this->mappingTable]
            )->getValue()
        );
    }
}
