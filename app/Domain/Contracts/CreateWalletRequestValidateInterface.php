<?php

namespace App\Domain\Contracts;

use App\Application\DTO\Wallet\CreateWalletDTO;

interface CreateWalletRequestValidateInterface
{
    public function validate(array $data): CreateWalletDTO;
}
