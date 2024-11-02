<?php declare(strict_types = 1);

namespace App\Normalizer\Value;

class StringValue implements ValueInterface
{
    public function __construct(private readonly ?string $value = null) {}

    public function getValue(): ?string
    {
        return $this->value;
    }
}
