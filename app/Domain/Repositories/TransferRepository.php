<?php

declare(strict_types=1);

namespace App\Domain\Repositories;

use App\Enums\TransferStatusEnum;
use App\Models\Transfer;
use Illuminate\Support\Facades\DB;

class TransferRepository
{
    public function register(array $array): Transfer
    {
        // TODO: verificar horaio registrado
        return Transfer::create($array);
    }

    public function updateTransferToRefund(Transfer $transfer): void
    {
        Transfer::where('id', $transfer->id)->update([
            'status' => TransferStatusEnum::STATUS_REFUND->value,
            'refunded_at' => now()->format('Y-m-d H:i:s')
        ]);
    }
}
