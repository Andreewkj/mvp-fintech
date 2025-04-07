<?php

namespace App\Domain\Requests;

use App\Domain\Interfaces\RequestValidateInterface;
use InvalidArgumentException;

readonly class CreateTransferRequest implements RequestValidateInterface
{
    public function __construct(
        private array $data
    ) {
        $this->validate();
    }

    public function validate(): array
    {
        if (empty($this->data['payee_id'])) {
            throw new InvalidArgumentException('Payee id is required');
        }

        if (empty($this->data['value'])) {
            throw new InvalidArgumentException('Value is required');
        }

        if (gettype($this->data['value']) !== 'integer') {
            throw new InvalidArgumentException('Value must be an integer');
        }

        if (gettype($this->data['payee_id']) !== 'string') {
            throw new InvalidArgumentException('Payee id must be a string');
        }

        return $this->data;
    }
}
