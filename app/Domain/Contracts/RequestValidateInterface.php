<?php

namespace App\Domain\Contracts;

interface RequestValidateInterface
{
    public function validate(array $data): array;
}
