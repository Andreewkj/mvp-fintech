<?php

declare(strict_types=1);

namespace App\Domain\Services;

use App\Domain\Entities\Cnpj;
use App\Domain\Entities\Cpf;
use App\Domain\Repositories\UserRepository;
use App\Models\User;

class UserService
{
    protected UserRepository $userRepository;

    public function __construct()
    {
        $this->userRepository = new UserRepository();
    }

    public function createUser(array $data): User
    {
        return $this->userRepository->create($data);
    }

    public function updateUserWallet(string $userId, string $walletId): void
    {
        $this->userRepository->updateUserWallet($userId, $walletId);
    }

    public function findUserByWalletId(string $id): ?User
    {
        return $this->userRepository->findUserByWalletId($id);
    }

    public function findUserById(string $id): ?User
    {
        return $this->userRepository->findUserById($id);
    }

    public function findUserByCpf(string $cpf): ?User
    {
        return $this->userRepository->findUserByCpf($cpf);
    }

    public function findUserByEmail(string $cpf): ?User
    {
        return $this->userRepository->findUserByEmail($cpf);
    }

    public function findUserByCnpj(string $cnpj) : ?User
    {
        return $this->userRepository->findUserByCnpj($cnpj);
    }
}
