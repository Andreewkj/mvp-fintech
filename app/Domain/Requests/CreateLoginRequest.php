<?php

namespace App\Domain\Requests;

use App\Domain\VO\Email;
use App\Domain\VO\Password;
use App\Domain\Interfaces\RequestValidateInterface;
use InvalidArgumentException;

class CreateLoginRequest implements RequestValidateInterface
{
    public function __construct(
        private array $data
    ) {
        $this->validate();
    }
    public function validate(): array
    {
        if (empty($this->data['email'])) {
            throw new InvalidArgumentException('Email is required');
        }

        if (empty($this->data['password'])) {
            throw new InvalidArgumentException('Password is required');
        }

        $this->data['password'] = (new Password($this->data['password']))->getValue();

        $this->data['email'] = (new Email($this->data['email']))->getValue();

        return $this->data;
    }
}
