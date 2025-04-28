<?php

declare(strict_types=1);

namespace App\Infra\Repositories;

use App\Domain\Contracts\Repositories\TransferRepositoryInterface;
use App\Domain\Entities\Transfer;
use App\Domain\Enums\TransferStatusEnum;
use App\Infra\Mappers\TransferMapper;
use App\Models\TransferModel;

class TransferRepository implements TransferRepositoryInterface
{
    public function __construct(protected TransferModel $model)
    {}

    public function register(array $array): ?Transfer
    {
        $model = $this->model->create($array);
        $model->refresh();

        return TransferMapper::toEntity($model);
    }

    public function updateToDeniedStatus(Transfer $transfer): void
    {
        $this->model->where('id', $transfer->getId())->update([
            'status' => TransferStatusEnum::STATUS_DENIED->value,
            'denied_at' => now()->format('Y-m-d H:i:s'),
        ]);
    }

    public function updateToAuthorizedStatus(Transfer $transfer): void
    {
        $this->model->where('id', $transfer->getId())->update([
            'status' => TransferStatusEnum::STATUS_AUTHORIZED->value,
            'authorized_at' => now()->format('Y-m-d H:i:s'),
        ]);
    }
}
