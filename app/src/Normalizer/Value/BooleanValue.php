<?php declare(strict_types = 1);

namespace App\Normalizer\Value;

class BooleanValue implements ValueInterface
{
    public function __construct(private readonly ?bool $value = null) {}

    public function getValue(): ?bool
    {
        return $this->value;
    }
}
