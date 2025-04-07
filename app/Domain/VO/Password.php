<?php

declare(strict_types=1);

namespace App\Domain\VO;

use InvalidArgumentException;

readonly class Password
{
    public function __construct(
        private string $password,
    ) {
        $this->validate($password);
    }

    private function validate(string $password): void
    {
        if (strlen($password) < 6) {
            throw new InvalidArgumentException('The password must have at least 6 characters');
        }
    }

    public function getValue(): string
    {
        return $this->password;
    }
}
