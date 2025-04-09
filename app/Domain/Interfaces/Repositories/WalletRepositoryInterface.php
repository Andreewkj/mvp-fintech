<?php

namespace App\Domain\Interfaces\Repositories;

use App\Models\Wallet;

interface WalletRepositoryInterface
{
    public function updatePayeeWallet(Wallet $payeeWallet, int $value) : void;

    public function updatePayerWallet(Wallet $payerWallet, int $value) : void;

    public function create(array $data) : Wallet;

    public function findWalletByUserId(string $userId) : ?Wallet;

    public function userWalletExist(string $userId) : bool;

    public function chargebackPayeeAmount(string $payeeId, int $amount): void;

    public function chargebackPayerAmount(string $payerId, int $amount): void;
}
