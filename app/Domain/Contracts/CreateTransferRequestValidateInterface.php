<?php

namespace App\Domain\Contracts;

use App\Application\DTO\Transfer\MakeTransferDTO;

interface CreateTransferRequestValidateInterface
{
    public function validate(array $data): MakeTransferDTO;
}
