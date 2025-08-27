<?php

namespace App\Enums;

enum EntityTypeEnum: string
{
    case client = 'client';
    case partner = 'partner';
    case award = 'award';

    public static function options(): array
    {
        return [
            self::client->value => 'Client',
            self::partner->value => 'Partner',
            self::award->value => 'Award',
        ];
    }
}
