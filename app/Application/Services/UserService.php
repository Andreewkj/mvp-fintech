<?php

declare(strict_types=1);

namespace App\Application\Services;

use App\Application\DTO\CreateUserDto;
use App\Application\Factories\UserFactory;
use App\Domain\Contracts\Repositories\UserRepositoryInterface;
use App\Domain\Entities\User;
use App\Domain\VO\Cnpj;
use App\Domain\VO\Cpf;
use App\Domain\VO\Email;

class UserService
{
    public function __construct(
        protected UserRepositoryInterface $userRepository
    ) {}

    /**
     * @param CreateUserDto $createUserDto
     * @return User
     */
    public function createUser(CreateUserDto $createUserDto): User
    {
        $userEntity = UserFactory::fromDto($createUserDto);

        return $this->userRepository->create($userEntity, $createUserDto->password);
    }

    /**
     * @param string $cpf
     * @return User | null
     */
    public function findUserByCpf(string $cpf): ?User
    {
        $cpf = (new Cpf($cpf))->getValue();
        return $this->userRepository->findUserByCpf($cpf);
    }

    /**
     * @param string $email
     * @return User | null
     */
    public function findUserByEmail(string $email): ?User
    {
        $email = (new Email($email))->getValue();
        return $this->userRepository->findUserByEmail($email);
    }

    /**
     * @param string $cnpj
     * @return User | null
     */
    public function findUserByCnpj(string $cnpj): ?User
    {
        $cnpj = (new Cnpj($cnpj))->getValue();

        return $this->userRepository->findUserByCnpj($cnpj);
    }
}
