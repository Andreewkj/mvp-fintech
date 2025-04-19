<?php

declare(strict_types=1);

namespace App\Domain\VO;

use InvalidArgumentException;

class TransferValue
{
    private int $value;

    public function __construct(int $value)
    {
        if ($value <= 0) {
            throw new InvalidArgumentException('Transfer value must be greater than 0.');
        }

        $this->value = $value;
    }

    public function getValue(): int
    {
        return $this->value;
    }
}
