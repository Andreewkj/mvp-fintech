<?php

namespace App\Domain\Contracts\Repositories;

use App\Domain\Entities\Wallet;

interface WalletRepositoryInterface
{
    public function create(Wallet $wallet) : Wallet;

    public function findWalletByUserId(string $userId) : ?Wallet;

    public function userWalletExist(string $userId) : bool;

    public function findUserByWalletById(string $walletId) : ?Wallet;

    public function updateBalance(Wallet $wallet): void;

    public function findById(string $getPayeeWalletId) : ?Wallet;
}
