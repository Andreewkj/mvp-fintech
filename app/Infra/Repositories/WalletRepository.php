<?php

declare(strict_types=1);

namespace App\Infra\Repositories;

use App\Domain\Interfaces\Repositories\WalletRepositoryInterface;
use App\Infra\Mappers\WalletMapper;
use App\Models\WalletModel;
use Illuminate\Support\Facades\Cache;
use App\Domain\Entities\Wallet;

class WalletRepository implements WalletRepositoryInterface
{
    public function __construct(protected WalletModel $model)
    {}

    public function save(Wallet $entity): void
    {
        $model = $this->model->find($entity->getId());

        if (!$model) {
            throw new \Exception('Wallet not found');
        }

        $model = WalletMapper::toModel($entity, $model);
        $model->save();
    }

    public function updatePayeeWalletById(String $payeeWalletId, int $value) : void
    {
        $lock = Cache::lock('wallet:' . $payeeWalletId . ':lock', 5);
        if ($lock->get()) {
            try {
                $payeeWallet = $this->model->where('id', $payeeWalletId)->first();
                $payeeWallet->balance += $value;
                $payeeWallet->save();
            } finally {
                $lock->release();
            }
        }
    }

    public function updatePayerWalletById(String $payerWalletId, int $value) : void
    {
        $lock = Cache::lock('wallet:' . $payerWalletId . ':lock', 5);
        if ($lock->get()) {
            try {
                $payerWallet = $this->model->where('id', $payerWalletId)->first();
                $payerWallet->balance -= $value;
                $payerWallet->save();
            } finally {
                $lock->release();
            }
        }
    }

    public function create(array $data) : Wallet
    {
        $model = $this->model->create($data);
        return WalletMapper::toEntity($model);
    }

    public function findWalletByUserId(string $userId) : ?Wallet
    {
        $model = $this->model->where('user_id', $userId)->first();
        return $model ? WalletMapper::toEntity($model) : null;

    }

    public function userWalletExist(string $userId) : bool
    {
        return $this->model->where('user_id', $userId)->exists();
    }

    public function findUserByWalletById(string $walletId) : ?Wallet
    {
        $model = $this->model->where('id', $walletId)->first();
        return $model ? WalletMapper::toEntity($model) : null;
    }
}
