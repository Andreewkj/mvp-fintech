<?php

declare(strict_types=1);

namespace App\Domain\VO;

use Illuminate\Support\Str;
use InvalidArgumentException;

class EntityId
{
    private string $value;

    public function __construct(?string $value = null)
    {
        $value = $value ?? Str::ulid()->toString();

        if (!$this->isValidUlid($value)) {
            throw new InvalidArgumentException("Invalid ID format: $value");
        }

        $this->value = $value;
    }

    public function getValue(): string
    {
        return $this->value;
    }

    private function isValidUlid(string $value): bool
    {
        return preg_match('/^[0-9A-HJKMNP-TV-Z]{26}$/i', $value) === 1;
    }
}
