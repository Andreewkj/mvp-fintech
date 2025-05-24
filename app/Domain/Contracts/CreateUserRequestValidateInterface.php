<?php

namespace App\Domain\Contracts;

use App\Application\DTO\User\CreateUserDTO;

interface CreateUserRequestValidateInterface
{
    public function validate(array $data): CreateUserDTO;
}
