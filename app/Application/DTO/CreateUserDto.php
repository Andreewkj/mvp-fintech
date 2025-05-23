<?php

namespace App\Application\DTO;

readonly class CreateUserDto
{
    public function __construct(
        public string  $email,
        public string  $fullName,
        public ?string $cpf,
        public ?string $cnpj,
        public string  $phone,
        public string  $password
    ) {}
}
