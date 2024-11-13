<?php

namespace App\Tests\Functional;

use App\Normalizer\Value\StringValue;
use App\Normalizer\Value\ValueInterface;
use App\Reflection\CustomPropertyTypeExtractor;
use PHPUnit\Framework\TestCase;

class PropertyTypeExtractorTest extends TestCase
{
    private CustomPropertyTypeExtractor $propertyTypeExtractor;

    public function setUp(): void
    {
        $this->propertyTypeExtractor = new CustomPropertyTypeExtractor();
    }

    public function testPropertyA()
    {
        $types = $this->propertyTypeExtractor->getTypes(DummyClass::class, 'propertyA');

        $this->assertSame('string', $types[0]->getBuiltinType());
        $this->assertTrue($types[0]->isNullable());
    }

    public function testPropertyB()
    {
        $types = $this->propertyTypeExtractor->getTypes(DummyClass::class, 'propertyB');

        $this->assertSame('object', $types[0]->getBuiltinType());
        $this->assertSame(StringValue::class, $types[0]->getClassName());
        $this->assertTrue($types[0]->isNullable());
    }

    public function testPropertyC()
    {
        $types = $this->propertyTypeExtractor->getTypes(DummyClass::class, 'propertyC');

        $this->assertSame('object', $types[0]->getBuiltinType());
        $this->assertSame(ValueInterface::class, $types[0]->getClassName());
        $this->assertFalse($types[0]->isNullable());

        $this->assertSame('string', $types[1]->getBuiltinType());
        $this->assertFalse($types[1]->isNullable());

        $this->assertSame('int', $types[2]->getBuiltinType());
        $this->assertFalse($types[2]->isNullable());
    }

    public function testPropertyD()
    {
        $types = $this->propertyTypeExtractor->getTypes(DummyClass::class, 'propertyD');

        $this->assertSame('string', $types[0]->getBuiltinType());
        $this->assertTrue($types[0]->isNullable());
    }

    public function testPropertyE()
    {
        $types = $this->propertyTypeExtractor->getTypes(DummyClass::class, 'propertyE');

        $this->assertNull($types);
    }
}

class DummyClass extends DummyClassExtended
{
    private ?string $propertyA;

    private ?StringValue $propertyB;

    private string|int|ValueInterface $propertyC;
}

class DummyClassExtended
{
    protected ?string $propertyD;

    protected mixed $propertyE;
}
