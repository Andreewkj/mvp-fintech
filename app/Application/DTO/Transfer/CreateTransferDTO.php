<?php

declare(strict_types=1);

namespace App\Application\DTO\Transfer;

use App\Domain\Entities\Wallet;

class CreateTransferDTO
{
    public function __construct(
        public Wallet $payerWallet,
        public Wallet $payeeWallet,
        public int $value
    ) {}
}
