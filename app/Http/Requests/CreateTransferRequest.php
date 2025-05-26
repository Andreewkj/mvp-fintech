<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Application\DTO\Transfer\MakeTransferDTO;
use App\Domain\Contracts\CreateTransferRequestValidateInterface;
use InvalidArgumentException;

readonly class CreateTransferRequest implements CreateTransferRequestValidateInterface
{
    public function validate(array $data): MakeTransferDTO
    {
        $this->validateRequiredFields($data);

        return new MakeTransferDTO(
            $data['payer_id'],
            $data['payee_id'],
            $data['value']
        );
    }

    private function validateRequiredFields(array $data): void
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
    }
}
