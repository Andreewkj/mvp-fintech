<?php

declare(strict_types=1);

namespace App\Application\DTO\User;

class LoginUserDTO
{
    public function __construct(
        public string $email,
        public string $password
    ) {}

    public function toArray(): array
    {
        return [
            'email' => $this->email,
            'password' => $this->password
        ];
    }
}
