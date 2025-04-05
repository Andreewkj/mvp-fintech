<?php

namespace App\Domain\Interfaces;

use App\Models\Transfer;

interface BankAdapterInterface
{
    public function authorizeTransfer(Transfer $transfer): void;
}
