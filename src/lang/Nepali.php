<?php

namespace Dipesh\NepaliDate\lang;

use Dipesh\NepaliDate\Contracts\Language;

class Nepali implements Language
{
    public static string $gate = 'गते';

    public static array $digits = [
        '०',
        '१',
        '२',
        '३',
        '४',
        '५',
        '६',
        '७',
        '८',
        '९',
    ];

    public static array $weeks = [
        [
            'l' => 'आइतबार',
            'D' => 'आइत',
        ],
        [
            'l' => 'सोमबार',
            'D' => 'सोम',
        ],
        [
            'l' => 'मंगलबार',
            'D' => 'मंगल',
        ],
        [
            'l' => 'बुधबार',
            'D' => 'बुध',
        ],
        [
            'l' => 'बिहिबार',
            'D' => 'बिहि',
        ],
        [
            'l' => 'शुक्रबार',
            'D' => 'शुक्र',
        ],
        [
            'l' => 'शनिबार',
            'D' => 'शनि',
        ],
    ];

    public static array $months = [
        'बैसाख',
        'जेठ',
        'असार',
        'श्रावन',
        'भाद्र',
        'असोज',
        'कार्तिक',
        'मंसिर',
        'पुष',
        'माघ',
        'फाल्गुण',
        'चैत्र',
    ];

    public function getGate(): string
    {
        return self::$gate;
    }

    public function getDigit(int $digit): int|string
    {
        return self::$digits[$digit];
    }

    public function getWeek(int $week): array
    {
        return self::$weeks[$week];
    }

    public function getMonth(int $month): int|string
    {
        return self::$months[$month];
    }
}
