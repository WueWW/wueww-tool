<?php

namespace App\Enum;

use Elao\Enum\AutoDiscoveredValuesTrait;
use Elao\Enum\ReadableEnum;

final class SlackTimeEnum extends ReadableEnum
{
    use AutoDiscoveredValuesTrait;

    public const NEIN = 'nein';
    public const P10 = '10%';
    public const P20 = '20%';

    public static function readables(): array
    {
        return [
            self::NEIN => 'nein',
            self::P10 => '10% (ein Tag alle zwei Wochen)',
            self::P20 => '20% (ein Tag jede Woche)',
        ];
    }
}
