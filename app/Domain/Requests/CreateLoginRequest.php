<?php

namespace App\Domain\Requests;

use App\Domain\VO\Cnpj;
use App\Domain\VO\Cpf;
use App\Domain\VO\Email;
use App\Domain\VO\Password;
use App\Domain\Interfaces\RequestValidateInterface;
use App\Domain\Services\UserService;

class CreateLoginRequest implements RequestValidateInterface
{
    private UserService $userService;
    public function __construct(
        private array $data
    ) {
        $this->userService = new UserService();
        $this->validate();
    }
    public function validate(): array
    {
        if (empty($this->data['email'])) {
            throw new \InvalidArgumentException('Email is required');
        }

        if (empty($this->data['password'])) {
            throw new \InvalidArgumentException('Password is required');
        }

        $this->data['password'] = (new Password($this->data['password']))->getValue();

        $this->data['email'] = (new Email($this->data['email']))->getValue();

        if ($this->userService->findUserByEmail($this->data['email']) !== null) {
            throw new \InvalidArgumentException('User already registered');
        }

        return $this->data;
    }
}
