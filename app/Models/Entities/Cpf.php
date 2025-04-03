<?php

namespace App\Models\Entities;

readonly class Cpf
{
    public function __construct(
        private string $cpf,
    ) {
        $this->validate($cpf);
    }

    public function validate(string $cpf): void
    {
        $cpf = preg_replace('/[^0-9]/', '', $cpf);

        //TODO conferir regra depois

        // Check if the CPF number is the correct length
        if (strlen($cpf) != 11) {
            throw new \InvalidArgumentException('Invalid CPF length');
        }

        // Prevents a sequence of repeated numbers like "11111111111" from being considered valid
        if (preg_match('/(\d)\1{10}/', $cpf)) {
            throw new \InvalidArgumentException('Invalid CPF sequence');
        }

        // Calculate the first verification digit
        $sum = 0;
        for ($i = 0; $i < 9; $i++) {
            $sum += $cpf[$i] * (10 - $i);
        }
        $remainder = ($sum * 10) % 11;
        if ($remainder == 10 || $remainder == 11) {
            $remainder = 0;
        }
        if ($remainder != $cpf[9]) {
            throw new \InvalidArgumentException('Invalid CPF format');
        }

        // Calculate the second verification digit
        $sum = 0;
        for ($i = 0; $i < 10; $i++) {
            $sum += $cpf[$i] * (11 - $i);
        }
        $remainder = ($sum * 10) % 11;
        if ($remainder == 10 || $remainder == 11) {
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
