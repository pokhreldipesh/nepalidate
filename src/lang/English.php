<?php

declare(strict_types=1);

namespace Dipesh\NepaliDate\lang;

use Dipesh\NepaliDate\Contracts\Language;

/**
 * English / Romanized language pack.
 *
 * All data is immutable (class constants). Instantiate freely —
 * the class holds no mutable state.
 */
class English implements Language
{
    public const GATE = 'Gate';

    public const DIGITS = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];

    public const WEEKS = [
        ['l' => 'Aaitabar', 'D' => 'Aaita'],
        ['l' => 'Sombar', 'D' => 'Som'],
        ['l' => 'Mangalbar', 'D' => 'Mangal'],
        ['l' => 'Budhabar', 'D' => 'Budh'],
        ['l' => 'Bihibar', 'D' => 'Bihi'],
        ['l' => 'Sukrabar', 'D' => 'Sukra'],
        ['l' => 'Sanibar', 'D' => 'Sani'],
    ];

    public const MONTHS = [
        ['F' => 'Baishakh', 'M' => ''],
        ['F' => 'Jestha', 'M' => ''],
        ['F' => 'Ashar', 'M' => ''],
        ['F' => 'Shrawan', 'M' => ''],
        ['F' => 'Bhadra', 'M' => ''],
        ['F' => 'Ashoj', 'M' => ''],
        ['F' => 'Kartik', 'M' => ''],
        ['F' => 'Mangshir', 'M' => ''],
        ['F' => 'Poush', 'M' => ''],
        ['F' => 'Magh', 'M' => ''],
        ['F' => 'Falgun', 'M' => ''],
        ['F' => 'Chaitra', 'M' => ''],
    ];

    public function getGate(): string
    {
        return self::GATE;
    }

    public function getDigit(int $digit): int|string
    {
        return self::DIGITS[$digit];
    }

    /**
     * @return array{l: string, D: string}
     */
    public function getWeek(int $week): array
    {
        return self::WEEKS[$week];
    }

    /**
     * @return array{F: string, M: string}
     */
    public function getMonth(int $month): array
    {
        return self::MONTHS[$month];
    }
}
