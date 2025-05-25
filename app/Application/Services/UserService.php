<?php

declare(strict_types=1);

namespace App\Application\Services;

use App\Application\DTO\User\CreateUserDTO;
use App\Application\Factories\UserFactory;
use App\Domain\Contracts\Repositories\UserRepositoryInterface;
use App\Domain\Entities\User;
use App\Domain\VO\Cnpj;
use App\Domain\VO\Cpf;
use App\Domain\VO\Email;

readonly class UserService
{
    public function __construct(
        private UserRepositoryInterface $userRepository,
        private UserFactory             $userFactory
    ) {}

    public function createUser(CreateUserDTO $createUserDto): User
    {
        $userEntity = $this->userFactory->fromDto($createUserDto);
        return $this->userRepository->create($userEntity, $createUserDto->password);
    }

    public function findUserByCpf(string $cpf): ?User
    {
        $cpf = (new Cpf($cpf))->getValue();
        return $this->userRepository->findUserByCpf($cpf);
    }

    public function findUserByEmail(string $email): ?User
    {
        $email = (new Email($email))->getValue();
        return $this->userRepository->findUserByEmail($email);
    }

    public function findUserByCnpj(string $cnpj): ?User
    {
        $cnpj = (new Cnpj($cnpj))->getValue();
        return $this->userRepository->findUserByCnpj($cnpj);
    }
}
