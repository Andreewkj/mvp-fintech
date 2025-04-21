<?php

namespace App\Domain\Contracts\Repositories;

use App\Domain\Entities\User;

interface UserRepositoryInterface
{
    public function create(array $data): User;

    public function updateUserWallet(string $userId, string $walletId): void;

    public function findUserByWalletId(string $walletId): ?User;

    public function findUserByCpf(string $cpf): ?User;

    public function findUserByEmail(string $email): ?User;

    public function findUserByCnpj(string $cnpj): ?User;
}
