<?php

declare(strict_types=1);

namespace App\Infra\Repositories;

use App\Domain\Interfaces\Repositories\UserRepositoryInterface;
use App\Infra\Mappers\UserMapper;
use App\Models\UserModel;
use App\Domain\Entities\User;

class UserRepository implements UserRepositoryInterface
{
    public function __construct(protected UserModel $userModel)
    {}

    public function create(array $data): User
    {
        $model = $this->userModel->create($data);
        $model->refresh();
        return UserMapper::toEntity($model);
    }

    public function updateUserWallet(string $userId, string $walletId): void
    {
        $this->userModel->where('id', $userId)->update(['wallet_id' => $walletId]);
    }

    public function findUserByWalletId(string $walletId): ?User
    {
        $model = $this->userModel->where('wallet_id', $walletId)->first();
        return $model ? UserMapper::toEntity($model) : null;
    }

    public function findUserByEmail(string $email): ?User
    {
        $model = $this->userModel->where('email', $email)->first();
        return $model ? UserMapper::toEntity($model) : null;
    }

    public function findUserByCpf(string $cpf): ?User
    {
        $model = $this->userModel->where('cpf', $cpf)->first();
        return $model ? UserMapper::toEntity($model) : null;
    }

    public function findUserByCnpj(string $cnpj): ?User
    {
        $model = $this->userModel->where('cnpj', $cnpj)->first();
        return $model ? UserMapper::toEntity($model) : null;
    }
}
