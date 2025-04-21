<?php

namespace App\Http\Requests;

use App\Domain\Contracts\RequestValidateInterface;
use InvalidArgumentException;

readonly class CreateTransferRequest implements RequestValidateInterface
{
    public function validate(array $data): array
    {
        if (empty($data['payee_id'])) {
            throw new InvalidArgumentException('Payee id is required');
        }

        if (empty($data['value'])) {
            throw new InvalidArgumentException('Value is required');
        }

        if (gettype($data['value']) !== 'integer') {
            throw new InvalidArgumentException('Value must be an integer');
        }

        if (gettype($data['payee_id']) !== 'string') {
            throw new InvalidArgumentException('Payee id must be a string');
        }

        return $data;
    }
}
