<?php

namespace App\Domain\VO;

class Phone
{
    public function __construct(
        private string $phone,
    ) {
        $this->validate($phone);
    }

    private function validate(string $phone): void
    {
        $phone = preg_replace('/[^0-9]/', '', $phone);

        if (strlen($phone) < 10 || strlen($phone) > 11) {
            throw new \InvalidArgumentException('Invalid phone length');
        }

        $this->phone = $phone;
    }

    public function getValue(): string
    {
        return $this->phone;
    }
}
