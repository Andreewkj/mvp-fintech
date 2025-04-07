<?php

declare(strict_types=1);

namespace App\Domain\Repositories;

use App\Domain\Interfaces\WalletRepositoryInterface;
use App\Models\Wallet;
use Illuminate\Support\Facades\DB;

class WalletRepository implements WalletRepositoryInterface
{
    protected Wallet $model;

    public function __construct()
    {
        $this->model = new Wallet();
    }

    public function updatePayeeWallet(Wallet $payeeWallet, int $value) : void
    {
        $this->model->where('id', $payeeWallet->id)->update([
            'balance' => DB::raw("balance + {$value}")
        ]);
    }

    public function updatePayerWallet(Wallet $payerWallet, int $value) : void
    {
        $this->model->where('id', $payerWallet->id)->update([
            'balance' => DB::raw("balance - {$value}")
        ]);
    }

    public function create(array $data) : Wallet
    {
        return $this->model->create($data);
    }

    public function findWalletByUserId(string $id) : ?Wallet
    {
        return $this->model->where('user_id', $id)->first();
    }


    public function userWalletExist(string $id) : bool
    {
        return $this->model->where('user_id', $id)->exists();
    }

    public function chargebackPayeeAmount(string $payeeId, int $amount): void
    {
        $this->model->where('user_id', $payeeId)->update([
            'balance' => DB::raw("balance - {$amount}")
        ]);
    }

    public function chargebackPayerAmount(string $payerId, int $amount): void
    {
        $this->model->where('user_id', $payerId)->update([
            'balance' => DB::raw("balance + {$amount}")
        ]);
    }
}
