<?php

namespace App\Domain\Contracts;

use App\Application\DTO\User\LoginUserDTO;

interface LoginUserRequestValidateInterface
{
    public function validate(array $data): LoginUserDTO;
}
