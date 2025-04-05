<?php

namespace App\Enums;

enum TransferStatusEnum: String
{
    case STATUS_ACTIVE = 'active';
    case STATUS_REFUND = 'refund';

    public static function toArray(): array
    {
        return [
            self::STATUS_ACTIVE->value,
            self::STATUS_REFUND->value
        ];
    }
}
