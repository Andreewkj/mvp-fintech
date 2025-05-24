<?php

namespace App\Http\Requests;

use App\Application\DTO\User\LoginUserDTO;
use App\Domain\Contracts\LoginUserRequestValidateInterface;
use App\Domain\VO\Email;
use App\Domain\VO\Password;
use InvalidArgumentException;

class LoginUserRequest implements LoginUserRequestValidateInterface
{
    public function validate(array $data): LoginUserDTO
    {
        $this->validateRequiredFields($data);

        $data['password'] = (new Password($data['password']))->getValue();

        $data['email'] = (new Email($data['email']))->getValue();

        return new LoginUserDTO(
            $data['email'],
            $data['password']
        );
    }

    private function validateRequiredFields(array $data): void
    {
        if (empty($data['email'])) {
            throw new InvalidArgumentException('Email is required');
        }

        if (empty($data['password'])) {
            throw new InvalidArgumentException('Password is required');
        }
    }
}
