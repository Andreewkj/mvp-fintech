<?php

declare(strict_types=1);

namespace App\Domain\Enums;

enum TransferStatusEnum: String
{
    case STATUS_AUTHORIZED = 'authorized';
    case STATUS_DENIED = 'denied';
    case STATUS_PENDING = 'pending';

    public static function toArray(): array
    {
        return [
            self::STATUS_PENDING->value,
            self::STATUS_AUTHORIZED->value,
            self::STATUS_DENIED->value
        ];
    }
}
