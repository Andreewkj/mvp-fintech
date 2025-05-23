<?php

namespace App\Domain\Contracts;

use App\Application\DTO\CreateUserDto;

interface CreateUserRequestValidateInterface
{
    public function validate(array $data): CreateUserDto;
}
