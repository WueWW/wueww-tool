<?php

namespace App\Enum;

use Elao\Enum\AutoDiscoveredValuesTrait;
use Elao\Enum\ReadableEnum;

final class OeffiErreichbarkeitEnum extends ReadableEnum
{
    use AutoDiscoveredValuesTrait;

    public const JA = 'ja';
    public const GEHT_SO = 'geht so';
    public const NEIN = 'nein';

    public static function readables(): array
    {
        return [
            self::JA => 'ja (Haltestelle in nächster Nähe, regelmäßig angebunden)',
            self::GEHT_SO => 'geht so (ab und an kommt mal ein Bus)',
            self::NEIN => 'nein (ohne Auto eher schwierig)',
        ];
    }
}
