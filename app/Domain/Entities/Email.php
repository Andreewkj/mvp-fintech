<?php

declare(strict_types=1);

namespace App\Domain\Entities;

class Email
{
    public function __construct(
        private string $email,
    ) {
        $this->validate($email);
    }

    private function validate(string $email): void
    {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new \InvalidArgumentException('Invalid email');
        }

        $this->email = strTolower($email);
    }

    public function getValue(): string
    {
        return $this->email;
    }
}
