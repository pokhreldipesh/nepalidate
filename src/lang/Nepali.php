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
        [
            'F' => 'बैसाख',
            'M' => '',
        ],
        [
            'F' => 'जेठ',
            'M' => '',
        ],
        [
            'F' => 'असार',
            'M' => '',
        ],
        [
            'F' => 'साउन',
            'M' => '',
        ],
        [
            'F' => 'भदौ',
            'M' => '',
        ],
        [
            'F' => 'असोज',
            'M' => '',
        ],
        [
            'F' => 'कार्तिक',
            'M' => '',
        ],
        [
            'F' => 'मंसिर',
            'M' => '',
        ],
        [
            'F' => 'पुष',
            'M' => '',
        ],
        [
            'F' => 'माघ',
            'M' => '',
        ],
        [
            'F' => 'फाल्गुण',
            'M' => '',
        ],
        [
            'F' => 'चैत',
            'M' => '',
        ],
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

    public function getMonth(int $month): array
    {
        return self::$months[$month];
    }
}
