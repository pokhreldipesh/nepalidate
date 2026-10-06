<?php

declare(strict_types=1);

namespace Dipesh\NepaliDate\lang;

use Dipesh\NepaliDate\Contracts\Language;

/**
 * Nepali / Devanagari language pack.
 *
 * All data is immutable (class constants). Instantiate freely —
 * the class holds no mutable state.
 */
class Nepali implements Language
{
    public const GATE = 'गते';

    public const DIGITS = ['०', '१', '२', '३', '४', '५', '६', '७', '८', '९'];

    public const WEEKS = [
        ['l' => 'आइतबार', 'D' => 'आइत'],
        ['l' => 'सोमबार', 'D' => 'सोम'],
        ['l' => 'मंगलबार', 'D' => 'मंगल'],
        ['l' => 'बुधबार', 'D' => 'बुध'],
        ['l' => 'बिहिबार', 'D' => 'बिहि'],
        ['l' => 'शुक्रबार', 'D' => 'शुक्र'],
        ['l' => 'शनिबार', 'D' => 'शनि'],
    ];

    public const MONTHS = [
        ['F' => 'बैसाख', 'M' => ''],
        ['F' => 'जेठ', 'M' => ''],
        ['F' => 'असार', 'M' => ''],
        ['F' => 'साउन', 'M' => ''],
        ['F' => 'भदौ', 'M' => ''],
        ['F' => 'असोज', 'M' => ''],
        ['F' => 'कार्तिक', 'M' => ''],
        ['F' => 'मंसिर', 'M' => ''],
        ['F' => 'पुष', 'M' => ''],
        ['F' => 'माघ', 'M' => ''],
        ['F' => 'फाल्गुण', 'M' => ''],
        ['F' => 'चैत', 'M' => ''],
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
