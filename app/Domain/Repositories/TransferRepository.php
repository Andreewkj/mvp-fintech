<?php

declare(strict_types=1);

namespace App\Domain\Repositories;

use App\Domain\Interfaces\Repositories\TransferRepositoryInterface;
use App\Models\Transfer;

class TransferRepository implements TransferRepositoryInterface
{
    public function __construct(protected Transfer $model)
    {}

    public function register(array $array): ?Transfer
    {
        return $this->model->create($array);
    }

    public function update(Transfer $transfer): bool
    {
        return $transfer->save();
    }
}
