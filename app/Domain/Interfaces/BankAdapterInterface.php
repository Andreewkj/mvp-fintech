<?php

declare(strict_types=1);

namespace App\Domain\Interfaces;

use App\Models\Transfer;

interface BankAdapterInterface
{
    public function authorizeTransfer(Transfer $transfer): void;
}
