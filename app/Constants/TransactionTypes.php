<?php

namespace App\Constants;

final class TransactionTypes
{
    public const DEPOSIT = 'deposit';
    public const WITHDRAW = 'withdraw';

    public static function getList(): array
    {
        return [
            self::DEPOSIT => 'deposit',
            self::WITHDRAW => 'withdraw',
        ];
    }

    public static function getLabel($key): string
    {
        return array_key_exists($key, self::getList()) ? self::getList()[$key] : " ";
    }
}
