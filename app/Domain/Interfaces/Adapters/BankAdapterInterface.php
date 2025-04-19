<?php

declare(strict_types=1);

namespace App\Domain\Interfaces\Adapters;

use App\Domain\Entities\Transfer;

interface BankAdapterInterface
{
    public function authorizeTransfer(Transfer $transfer): void;
}
