<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Application\DTO\User\CreateUserDTO;
use App\Application\Services\UserService;
use App\Domain\Contracts\CreateUserRequestValidateInterface;
use App\Domain\VO\Cnpj;
use App\Domain\VO\Cpf;
use App\Domain\VO\Email;
use App\Domain\VO\Password;
use App\Domain\VO\Phone;
use InvalidArgumentException;

class CreateUserRequest implements CreateUserRequestValidateInterface
{
    public function __construct(
        protected UserService $userService
    ) {
    }

    /**
     * @param array $data
     * @return CreateUserDTO
     */
    public function validate(array $data): CreateUserDTO
    {
        $this->validateRequiredFields($data);
        $this->validateUniqueness($data);

        return new CreateUserDTO(
            email: (new Email($data['email']))->getValue(),
            fullName: $data['full_name'],
            cpf: !empty($data['cpf']) ? (new Cpf($data['cpf']))->getValue() : null,
            cnpj: !empty($data['cnpj']) ? (new Cnpj($data['cnpj']))->getValue() : null,
            phone: (new Phone($data['phone']))->getValue(),
            password: (new Password($data['password']))->getValue(),
        );
    }

    private function validateRequiredFields(array $data): void
    {
        if (empty($data['email'])) {
            throw new InvalidArgumentException('Email is required');
        }

        if (empty($data['full_name'])) {
            throw new InvalidArgumentException('Full name is required');
        }

        if (empty($data['phone'])) {
            throw new InvalidArgumentException('Phone is required');
        }

        if (empty($data['password'])) {
            throw new InvalidArgumentException('Password is required');
        }

        if (empty($data['cpf']) && empty($data['cnpj'])) {
            throw new InvalidArgumentException('Cpf or Cnpj is required');
        }
    }

    private function validateUniqueness(array $data): void
    {
        $email = (new Email($data['email']))->getValue();

        if ($this->userService->findUserByEmail($email) !== null) {
            throw new InvalidArgumentException('User already registered');
        }

        if (!empty($data['cnpj'])) {
            $cnpj = (new Cnpj($data['cnpj']))->getValue();

            if ($this->userService->findUserByCnpj($cnpj) !== null) {
                throw new InvalidArgumentException('User already registered');
            }
        }

        if (!empty($data['cpf'])) {
            $cpf = (new Cpf($data['cpf']))->getValue();

            if ($this->userService->findUserByCpf($cpf) !== null) {
                throw new InvalidArgumentException('User already registered');
            }
        }
    }
}
