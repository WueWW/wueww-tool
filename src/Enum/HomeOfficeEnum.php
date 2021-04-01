<?php

namespace App\Enum;

use Elao\Enum\AutoDiscoveredValuesTrait;
use Elao\Enum\ReadableEnum;

final class HomeOfficeEnum extends ReadableEnum
{
    use AutoDiscoveredValuesTrait;

    public const NEIN = 'nein';
    public const AUS_GRUENDEN_OKAY = 'aus Gründen';
    public const MAL_EIN_TAG = 'mal ein Tag';
    public const RETRO_UND_WORKSHOPS = 'Retro & Workshops';
    public const JA = 'ja';

    public static function readables(): array
    {
        return [
            self::NEIN => 'nein',
            self::AUS_GRUENDEN_OKAY => 'aus Gründen okay (Termine oder so)',
            self::MAL_EIN_TAG => 'ein Tag die Woche okay',
            self::RETRO_UND_WORKSHOPS => 'Retro & Workshops im Office, ansonsten gerne',
            self::JA => 'ja klar',
        ];
    }
}
