<?php

namespace App\Enums;

enum WalletTypeEnum : string
{
    case COMMON = 'common';
    case SHOP_KEEPER = 'shop_keeper';

    public static function toArray(): array
    {
        return [
            self::COMMON->value,
            self::SHOP_KEEPER->value,
        ];
    }
}
