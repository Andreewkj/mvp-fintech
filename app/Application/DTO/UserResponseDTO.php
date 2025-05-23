<?php

namespace App\Application\DTO;

use App\Domain\Entities\User;

readonly class UserResponseDTO
{
    public function __construct(
        public string  $id,
        public string  $fullName,
        public string  $email,
        public string  $phone,
        public ?string $cpf,
        public ?string $cnpj
    ) {}

    public static function fromEntity(User $user): self
    {
        return new self(
            id: $user->getId(),
            fullName: $user->getName(),
            email: $user->getEmail()->getValue(),
            phone: $user->getPhone()->getValue(),
            cpf: $user->getCpf()?->getValue(),
            cnpj: $user->getCnpj()?->getValue(),
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'full_name' => $this->fullName,
            'email' => $this->email,
            'phone' => $this->phone,
            'cpf' => $this->cpf,
            'cnpj' => $this->cnpj,
        ];
    }
}
