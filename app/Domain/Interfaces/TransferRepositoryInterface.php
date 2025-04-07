<?php

namespace App\Domain\Interfaces;

use App\Models\Transfer;

interface TransferRepositoryInterface
{
    public function register(array $array): ?Transfer;
    public function updateTransferToRefund(Transfer $transfer): void;

}
