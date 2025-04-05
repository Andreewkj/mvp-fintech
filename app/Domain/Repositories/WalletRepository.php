<?php

namespace App\Domain\Repositories;

use App\Models\Wallet;
use Illuminate\Support\Facades\DB;

class WalletRepository
{
    public function updatePayeeWallet(Wallet $payeeWallet, int $value) : void
    {
        DB::transaction(function () use ($payeeWallet, $value) {
            Wallet::where('id', $payeeWallet->id)->update([
                'balance' => DB::raw("balance + {$value}")
            ]);
        });
    }

    public function updatePayerWallet(Wallet $payerWallet, int $value) : void
    {
        DB::transaction(function () use ($payerWallet, $value) {
            Wallet::where('id', $payerWallet->id)->update([
                'balance' => DB::raw("balance - {$value}")
            ]);
        });
    }

    public function create(array $data) : Wallet
    {
        return Wallet::create($data);
    }

    public function findWalletByUserId(string $id) : Wallet
    {
        return Wallet::where('user_id', $id)->first();
    }


    public function userWalletExist(string $id) : bool
    {
        return Wallet::where('user_id', $id)->exists();
    }

    public function chargebackPayeeAmount(string $payeeId, int $amount): void
    {
        DB::transaction(function () use ($payeeId, $amount) {
            Wallet::where('id', $payeeId)->update([
                'balance' => DB::raw("balance - {$amount}")
            ]);
        });
    }

    public function chargebackPayerAmount(string $payerId, int $amount): void
    {
        DB::transaction(function () use ($payerId, $amount) {
            Wallet::where('id', $payerId)->update([
                'balance' => DB::raw("balance + {$amount}")
            ]);
        });
    }
}
