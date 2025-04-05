<?php

namespace App\Domain\Repositories;

use App\Models\User;

class UserRepository
{
    public function create(array $data)
    {
        return User::create($data);
    }

    public function findById(string $payee)
    {
        return User::find($payee);
    }

    public function updateUserWallet(string $userId, string $walletId): void
    {
        User::where('id', $userId)->update(['wallet_id' => $walletId]);
    }

    public function findUserByWalletId(string $id): User
    {
        return User::where('wallet_id', $id)->first();
    }
}
