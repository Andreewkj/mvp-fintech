<?php

namespace App\Models\Entities;

readonly class Cnpj
{
    public function __construct(
        private string $cnpj,
    ) {
        $this->validate($cnpj);
    }

    private function validate(string $cnpj): void
    {
        //TODO conferir regra depois
        if (!filter_var($cnpj, FILTER_VALIDATE_REGEXP, [
            'options' => [
                'regexp' => '/^\d{2}\.\d{3}\.\d{3}\/\d{4}-\d{2}$/',
            ],
        ])) {
            throw new \InvalidArgumentException('Invalid cnpj');
        }
    }

    public function getValue(): string
    {
        return $this->cnpj;
    }
}
