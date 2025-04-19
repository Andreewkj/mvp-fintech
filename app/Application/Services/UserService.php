<?php

declare(strict_types=1);

namespace App\Application\Services;

use App\Domain\Interfaces\Repositories\UserRepositoryInterface;
use App\Domain\Entities\User;

class UserService
{
    public function __construct(
        protected UserRepositoryInterface $userRepository
    ) {}

    public function createUser(array $data): User
    {
        return $this->userRepository->create($data);
    }

    public function findUserByCpf(string $cpf): ?User
    {
        return $this->userRepository->findUserByCpf($cpf);
    }

    public function findUserByEmail(string $email): ?User
    {
        return $this->userRepository->findUserByEmail($email);
    }

    public function findUserByCnpj(string $cnpj): ?User
    {
        return $this->userRepository->findUserByCnpj($cnpj);
    }
}
