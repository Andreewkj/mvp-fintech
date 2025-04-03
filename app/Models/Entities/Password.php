<?php

namespace App\Models\Entities;

readonly class Password
{
    public function __construct(
        private string $password,
    ) {
        $this->validate($password);
    }

    public function validate(string $password): void
    {
        if (strlen($password) < 6) {
            throw new \InvalidArgumentException('Invalid password');
        }
    }

    public function getValue(): string
    {
        return $this->password;
    }
}
