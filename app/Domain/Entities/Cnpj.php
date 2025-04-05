<?php

declare(strict_types=1);

namespace App\Domain\Entities;

class Cnpj
{
    public function __construct(
        private string $cnpj,
    ) {
        $this->validate($cnpj);
    }

    private function validate(string $cnpj): void
    {
        $cnpj = preg_replace('/[^0-9]/', '', $cnpj);

        if (strlen($cnpj) != 14) {
            throw new \InvalidArgumentException('Invalid CNPJ length');
        }

        $this->validateCheckDigits($cnpj);

        $this->cnpj = $cnpj;
    }

    private function validateCheckDigits(string $cnpj): void
    {
        $length = strlen($cnpj) - 2;
        $numbers = substr($cnpj, 0, $length);
        $digits = substr($cnpj, $length);
        $sum = 0;
        $pos = $length - 7;

        for ($i = $length; $i >= 1; $i--) {
            $sum += $numbers[$length - $i] * $pos--;
            if ($pos < 2) {
                $pos = 9;
            }
        }

        $result = $sum % 11 < 2 ? 0 : 11 - $sum % 11;
        if ($result != $digits[0]) {
            throw new \InvalidArgumentException("Invalid CNPJ");
        }

        $length += 1;
        $numbers = substr($cnpj, 0, $length);
        $sum = 0;
        $pos = $length - 7;
        for ($i = $length; $i >= 1; $i--) {
            $sum += $numbers[$length - $i] * $pos--;
            if ($pos < 2) {
                $pos = 9;
            }
        }

        $result = $sum % 11 < 2 ? 0 : 11 - $sum % 11;

        if ($result != $digits[1]) {
            throw new \InvalidArgumentException("Invalid CNPJ");
        }
    }

    public function getValue(): string
    {
        return $this->cnpj;
    }
}
