<?php

declare(strict_types=1);

namespace App\Infra\Repositories;

use App\Domain\Interfaces\Repositories\WalletRepositoryInterface;
use App\Exceptions\WalletException;
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

    /**
     * @throws WalletException
     */
    public function updateBalance(Wallet $wallet): void
    {
        $lock = Cache::lock("wallet:{$wallet->getId()}:lock", 5);

        if ($lock->get()) {
            try {
                $walletModel = $this->model->findOrFail($wallet->getId());
                $walletModel->balance = $wallet->getBalance();
                $walletModel->save();
            } finally {
                $lock->release();
            }
        } else {
            throw new WalletException('Could not acquire lock');
        }
    }

    public function findById(string $getPayeeWalletId) : ?Wallet
    {
        $model = $this->model->where('id', $getPayeeWalletId)->first();
        return $model ? WalletMapper::toEntity($model) : null;
    }

    public function create(array $data) : Wallet
    {
        $model = $this->model->create($data);
        $model->refresh();

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
