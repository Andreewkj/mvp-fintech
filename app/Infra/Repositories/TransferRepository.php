<?php

declare(strict_types=1);

namespace App\Infra\Repositories;

use App\Domain\Entities\Transfer;
use App\Domain\Interfaces\Repositories\TransferRepositoryInterface;
use App\Infra\Mappers\TransferMapper;
use App\Models\TransferModel;

class TransferRepository implements TransferRepositoryInterface
{
    public function __construct(protected TransferModel $model)
    {}

    public function register(array $array): ?Transfer
    {
        $model = $this->model->create($array);
        return TransferMapper::toEntity($model);
    }

    public function update(Transfer $transfer): bool
    {
        $transfer = TransferMapper::toModel($transfer);
        return $transfer->save();
    }
}
