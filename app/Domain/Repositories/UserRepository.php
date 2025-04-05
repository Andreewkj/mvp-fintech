<?php

declare(strict_types=1);

namespace App\Domain\Repositories;

use App\Models\User;

class UserRepository
{
    public function create(array $data): User
    {
        return User::create($data);
    }

    public function updateUserWallet(string $userId, string $walletId): void
    {
        User::where('id', $userId)->update(['wallet_id' => $walletId]);
    }

    public function findUserByWalletId(string $id): ?User
    {
        return User::where('wallet_id', $id)->first();
    }

    public function findUserById(string $id): ?User
    {
        return User::find($id);
    }

    public function findUserByCpf(string $cpf): ?User
    {
        return User::where('cpf', $cpf)->first();
    }

    public function findUserByEmail(string $cpf): ?User
    {
        return User::where('email', $cpf)->first();
    }

    public function findUserByCnpj(string $cnpj): ?User
    {
        return User::where('cnpj', $cnpj)->first();
    }
}
