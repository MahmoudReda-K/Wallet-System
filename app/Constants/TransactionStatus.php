<?php

namespace App\Constants;

final class TransactionStatus
{
    public const PENDING = 'pending';
    public const DONE = 'done';

    public static function getList(): array
    {
        return [
            self::PENDING => 'pending',
            self::DONE => 'done',
        ];
    }

    public static function getLabel($key): string
    {
        return array_key_exists($key, self::getList()) ? self::getList()[$key] : " ";
    }
}
