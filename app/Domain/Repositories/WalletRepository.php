<?php

namespace App\Domain\Repositories;

use App\Models\User;
use App\Models\Wallet;
use Illuminate\Support\Facades\DB;

class WalletRepository
{
    public function updatePayeeWallet(User $payee, int $value) : void
    {
        Wallet::where('user_id', $payee->id)->update([
            'balance' => DB::raw("balance + {$value}")
        ]);
    }

    public function updatePayerWallet(User $payer, int $value)
    {
        Wallet::where('user_id', $payer->id)->update([
            'balance' => DB::raw("balance - {$value}")
        ]);
    }

    public function create(array $data) : Wallet
    {
        return Wallet::create($data);
    }
}
