<?php

namespace App\Enum;

use Elao\Enum\AutoDiscoveredValuesTrait;
use Elao\Enum\ReadableEnum;

final class GehaltsvorstellungEnum extends ReadableEnum
{
    use AutoDiscoveredValuesTrait;

    public const F35T45 = '35k - 45k';
    public const F40T50 = '40k - 50k';
    public const F45T55 = '45k - 55k';
    public const F50T60 = '50k - 60k';
    public const F55T65 = '55k - 65k';
    public const F60T70 = '60k - 70k';
    public const F65 = '65k+';

    public static function readables(): array
    {
        return [
            self::F35T45 => '35k - 45k',
            self::F40T50 => '40k - 50k',
            self::F45T55 => '45k - 55k',
            self::F50T60 => '50k - 60k',
            self::F55T65 => '55k - 65k',
            self::F60T70 => '60k - 70k',
            self::F65 => '65k+',
        ];
    }
}
