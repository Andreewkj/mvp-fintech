<?php

declare(strict_types=1);

namespace App\Domain\Repositories;

use App\Domain\Interfaces\Repositories\WalletRepositoryInterface;
use App\Models\Wallet;

class WalletRepository implements WalletRepositoryInterface
{
    protected Wallet $model;

    public function __construct()
    {
        $this->model = new Wallet();
    }

    public function updatePayeeWallet(Wallet $payeeWallet, int $value) : void
    {
        $payeeWallet->balance += $value;
        $payeeWallet->save();
    }

    public function updatePayerWallet(Wallet $payerWallet, int $value) : void
    {
        $payerWallet->balance -= $value;
        $payerWallet->save();
    }

    public function create(array $data) : Wallet
    {
        return $this->model->create($data);
    }

    public function findWalletByUserId(string $userId) : ?Wallet
    {
        return $this->model->where('user_id', $userId)->first();
    }

    public function userWalletExist(string $userId) : bool
    {
        return $this->model->where('user_id', $userId)->exists();
    }

    public function chargebackPayeeAmount(string $payeeId, int $amount): void
    {
        $wallet = $this->model->where('user_id', $payeeId)->first();
        $wallet->balance -= $amount;
        $wallet->save();
    }

    public function chargebackPayerAmount(string $payerId, int $amount): void
    {
        $wallet = $this->model->where('user_id', $payerId)->first();
        $wallet->balance += $amount;
        $wallet->save();
    }
}
