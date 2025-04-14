<?php

declare(strict_types=1);

namespace App\Domain\Services;

use App\Domain\Interfaces\Repositories\UserRepositoryInterface;
use App\Models\User;

class UserService
{
    public function __construct(
        protected UserRepositoryInterface $userRepository
    ) {}

    public function createUser(array $data): User
    {
        return $this->userRepository->create($data);
    }

    public function updateUserWallet(string $userId, string $walletId): void
    {
        $this->userRepository->updateUserWallet($userId, $walletId);
    }

    public function findUserByWalletId(string $walletId): ?User
    {
        return $this->userRepository->findUserByWalletId($walletId);
    }

    public function findUserById(string $userId): ?User
    {
        return $this->userRepository->findUserById($userId);
    }

    public function findUserByCpf(string $cpf): ?User
    {
        return $this->userRepository->findUserByCpf($cpf);
    }

    public function findUserByEmail(string $email): ?User
    {
        return $this->userRepository->findUserByEmail($email);
    }

    public function findUserByCnpj(string $cnpj) : ?User
    {
        return $this->userRepository->findUserByCnpj($cnpj);
    }
}
