<?php

namespace App\Domain\Contracts\Repositories;

use App\Domain\Entities\User;

interface UserRepositoryInterface
{
    public function create(User $userEntity, string $password): User;

    public function updateUserWallet(string $userId, string $walletId): void;

    public function findUserByWalletId(string $walletId): ?User;

    public function findUserByCpf(string $cpf): ?User;

    public function findUserByEmail(string $email): ?User;

    public function findUserByCnpj(string $cnpj): ?User;

    public function findUserById(string $userId): ?User;
}
