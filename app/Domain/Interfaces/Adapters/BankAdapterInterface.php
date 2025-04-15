<?php

declare(strict_types=1);

namespace App\Domain\Interfaces\Adapters;

use App\Models\TransferModel;

interface BankAdapterInterface
{
    public function authorizeTransfer(TransferModel $transfer): void;
}
