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

        if (preg_match('/(\d)\1{13}/', $cnpj)) {
            throw new \InvalidArgumentException('Invalid CNPJ format');
        }

        $this->validateFirstDigit($cnpj);

        $this->cnpj = $cnpj;
    }

    private function validateFirstDigit(string $cnpj): void
    {
        for ($t = 12; $t < 14; $t++) {
            for ($d = 0, $p = 5, $c = 0; $c < $t; $c++) {
                $d += $cnpj[$c] * $p;
                $p = ($p < 3) ? 9 : --$p;
            }
            $d = ((10 * $d) % 11) % 10;
            if ($cnpj[$c] != $d) {
                throw new \InvalidArgumentException('Invalid CNPJ format');
            }
        }
    }

    public function getValue(): string
    {
        return $this->cnpj;
    }
}
