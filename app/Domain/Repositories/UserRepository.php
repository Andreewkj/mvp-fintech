<?php

declare(strict_types=1);

namespace App\Domain\Repositories;

use App\Domain\Interfaces\UserRepositoryInterface;
use App\Models\User;

class UserRepository implements UserRepositoryInterface
{
    protected User $model;

    public function __construct()
    {
        $this->model = new User();
    }

    public function create(array $data): User
    {
        return $this->model->create($data);
    }

    public function updateUserWallet(string $userId, string $walletId): void
    {
        $this->model->where('id', $userId)->update(['wallet_id' => $walletId]);
    }

    public function findUserByWalletId(string $id): ?User
    {
        return $this->model->where('wallet_id', $id)->first();
    }

    public function findUserById(string $id): ?User
    {
        return $this->model->find($id);
    }

    public function findUserByCpf(string $cpf): ?User
    {
        return $this->model->where('cpf', $cpf)->first();
    }

    public function findUserByEmail(string $cpf): ?User
    {
        return $this->model->where('email', $cpf)->first();
    }

    public function findUserByCnpj(string $cnpj): ?User
    {
        return $this->model->where('cnpj', $cnpj)->first();
    }
}
