<?php

namespace App\Domain\Interfaces\Repositories;

use App\Models\User;

interface UserRepositoryInterface
{
    public function create(array $data): User;

    public function updateUserWallet(string $userId, string $walletId): void;

    public function findUserByWalletId(string $walletId): ?User;

    public function findUserById(string $userId): ?User;

    public function findUserByCpf(string $cpf): ?User;

    public function findUserByEmail(string $cpf): ?User;

    public function findUserByCnpj(string $cnpj): ?User;
}
