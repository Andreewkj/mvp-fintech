<?php

namespace App\Domain\Interfaces\Repositories;

use App\Domain\Entities\Wallet;

interface WalletRepositoryInterface
{
    public function updatePayeeWalletById(string $payeeWalletId, int $value) : void;

    public function updatePayerWalletById(string $payerWalletId, int $value) : void;

    public function create(array $data) : Wallet;

    public function findWalletByUserId(string $userId) : ?Wallet;

    public function userWalletExist(string $userId) : bool;

    public function findUserByWalletById(string $walletId) : ?Wallet;
}
