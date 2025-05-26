<?php

declare(strict_types=1);

namespace App\Application\DTO\Transfer;

class MakeTransferDTO
{
    public function __construct(
        public string $payerId,
        public string $payeeId,
        public int $value
    ) {}
}
