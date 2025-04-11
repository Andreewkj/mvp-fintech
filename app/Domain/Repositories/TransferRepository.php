<?php

declare(strict_types=1);

namespace App\Domain\Repositories;

use App\Domain\Interfaces\Repositories\TransferRepositoryInterface;
use App\Enums\TransferStatusEnum;
use App\Models\Transfer;

class TransferRepository implements TransferRepositoryInterface
{
    public function __construct(protected Transfer $model)
    {}

    public function register(array $array): ?Transfer
    {
        return $this->model->create($array);
    }

    public function updateTransferToRefund(Transfer $transfer): void
    {
        $transfer->status = TransferStatusEnum::STATUS_REFUND->value;
        $transfer->refunded_at = now()->format('Y-m-d H:i:s');
        $transfer->save();
    }
}
