<?php

namespace App\Domain\Interfaces;

use App\Models\Wallet;
use Illuminate\Support\Facades\DB;

interface WalletRepositoryInterface
{
    public function updatePayeeWallet(Wallet $payeeWallet, int $value) : void;

    public function updatePayerWallet(Wallet $payerWallet, int $value) : void;

    public function create(array $data) : Wallet;

    public function findWalletByUserId(string $id) : ?Wallet;

    public function userWalletExist(string $id) : bool;

    public function chargebackPayeeAmount(string $payeeId, int $amount): void;

    public function chargebackPayerAmount(string $payerId, int $amount): void;
}
