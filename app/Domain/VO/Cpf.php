<?php

declare(strict_types=1);

namespace App\Domain\VO;

class Cpf
{
    public function __construct(
        private string $cpf,
    ) {
        $this->validate($cpf);
    }

    private function validate(string $cpf): void
    {
        $cpf = preg_replace('/[^0-9]/', '', $cpf);

        if (strlen($cpf) != 11) {
            throw new \InvalidArgumentException('Invalid CPF length');
        }

        $this->validateFirstDigit($cpf);
        $this->validateSecondDigit($cpf);

        $this->cpf = $cpf;
    }

    private function validateFirstDigit(string $cpf): void
    {
        $sum = 0;
        for ($i = 0; $i < 9; $i++) {
            $sum += $cpf[$i] * (10 - $i);
        }

        $remainder = ($sum * 10) % 11;

        if ($remainder == 10) {
            $remainder = 0;
        }

        if ($remainder != $cpf[9]) {
            throw new \InvalidArgumentException('Invalid CPF format');
        }
    }

    private function validateSecondDigit(string $cpf): void
    {
        $sum = 0;

        for ($i = 0; $i < 10; $i++) {
            $sum += $cpf[$i] * (11 - $i);
        }

        $remainder = ($sum * 10) % 11;

        if ($remainder == 10) {
            $remainder = 0;
        }

        if ($remainder != $cpf[10]) {
            throw new \InvalidArgumentException('Invalid CPF format');
        }
    }

    public function getValue(): string
    {
        return $this->cpf;
    }
}
