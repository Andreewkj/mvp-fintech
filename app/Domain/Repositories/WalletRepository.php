<?php

declare(strict_types=1);

namespace App\Domain\Repositories;

use App\Domain\Interfaces\Repositories\WalletRepositoryInterface;
use App\Models\Wallet;
use Illuminate\Support\Facades\Cache;

class WalletRepository implements WalletRepositoryInterface
{
    public function __construct(protected Wallet $model)
    {}

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

    public function findUserByWalletById(string $walletId) : ?Wallet
    {
        return $this->model->where('id', $walletId)->first();
    }

    public function chargebackPayeeValue(string $payeeId, int $value): void
    {
        $wallet = $this->model->where('user_id', $payeeId)->first();

        $lock = Cache::lock('wallet:' . $wallet->id . ':lock', 5);
        if ($lock->get()) {
            try {
                $wallet->balance -= $value;
                $wallet->save();
            } finally {
                $lock->release();
            }
        }
    }

    public function chargebackPayerValue(string $payerId, int $value): void
    {
        $wallet = $this->model->where('user_id', $payerId)->first();
        $lock = Cache::lock('wallet:' . $wallet->id . ':lock', 5);
        if ($lock->get()) {
            try {
                $wallet->balance += $value;
                $wallet->save();
            } finally {
                $lock->release();
            }
        }
    }
}
