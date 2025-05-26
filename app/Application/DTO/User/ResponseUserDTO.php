<?php

declare(strict_types=1);

namespace App\Application\DTO\User;

use App\Domain\Entities\User;

readonly class ResponseUserDTO
{
    public function __construct(
        public string  $userId,
        public string  $fullName,
        public string  $email,
        public string  $phone,
        public ?string $cpf,
        public ?string $cnpj
    ) {}

    public static function fromEntity(User $user): self
    {
        return new self(
            userId: $user->getUserId(),
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
            'id' => $this->userId,
            'full_name' => $this->fullName,
            'email' => $this->email,
            'phone' => $this->phone,
            'cpf' => $this->cpf,
            'cnpj' => $this->cnpj,
        ];
    }
}
