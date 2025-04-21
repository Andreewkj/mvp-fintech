<?php

namespace App\Http\Requests;

use App\Domain\Contracts\RequestValidateInterface;
use App\Domain\VO\Email;
use App\Domain\VO\Password;
use InvalidArgumentException;

class CreateLoginRequest implements RequestValidateInterface
{
    public function validate(array $data): array
    {
        if (empty($data['email'])) {
            throw new InvalidArgumentException('Email is required');
        }

        if (empty($data['password'])) {
            throw new InvalidArgumentException('Password is required');
        }

        $data['password'] = (new Password($data['password']))->getValue();

        $data['email'] = (new Email($data['email']))->getValue();

        return $data;
    }
}
