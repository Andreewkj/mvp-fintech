<?php

declare(strict_types=1);

namespace App\Application\DTO\Wallet;

class CreateWalletDTO
{
    public function __construct(
        public string $userId,
        public string $type,
        public int $balance
    ) {}

}
