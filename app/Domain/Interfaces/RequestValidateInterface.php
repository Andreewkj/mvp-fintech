<?php

namespace App\Domain\Interfaces;

interface RequestValidateInterface
{
    public function validate(array $data): array;
}
