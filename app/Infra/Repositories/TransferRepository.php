<?php

declare(strict_types=1);

namespace App\Infra\Repositories;

use App\Domain\Contracts\Repositories\TransferRepositoryInterface;
use App\Domain\Entities\Transfer;
use App\Domain\Enums\TransferStatusEnum;
use App\Infra\Mappers\TransferMapper;
use App\Models\TransferModel;

readonly class TransferRepository implements TransferRepositoryInterface
{
    public function __construct(private TransferModel $model)
    {}

    public function create(Transfer $transfer): ?Transfer
    {
        $transferModel = TransferMapper::toModel($transfer);
        $transferModel->save();

        return TransferMapper::toEntity($transferModel);
    }

    public function updateToDeniedStatus(Transfer $transfer): void
    {
        $this->model->query()->where('id', $transfer->getTransferId())->update([
            'status' => TransferStatusEnum::STATUS_DENIED->value,
            'denied_at' => now()->format('Y-m-d H:i:s'),
        ]);

        $this->model->refresh();
    }

    public function updateToAuthorizedStatus(Transfer $transfer): void
    {
        $this->model->query()->where('id', $transfer->getTransferId())->update([
            'status' => TransferStatusEnum::STATUS_AUTHORIZED->value,
            'authorized_at' => now()->format('Y-m-d H:i:s'),
        ]);

        $this->model->refresh();
    }
}
