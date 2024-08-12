<?php

namespace Dipesh\NepaliDate\lang;

use Dipesh\NepaliDate\Contracts\Language;

class English implements Language
{
    public static string $gate = 'Gate';

    public static array $digits = [
        '0',
        '1',
        '2',
        '3',
        '4',
        '5',
        '6',
        '7',
        '8',
        '9',
    ];

    public static array $weeks = [
        [
            'l' => 'Aaitabar',
            'D' => 'Aaita',
        ],
        [
            'l' => 'Sombar',
            'D' => 'Som',
        ],
        [
            'l' => 'Mangalbar',
            'D' => 'Mangal',
        ],
        [
            'l' => 'Budhabar',
            'D' => 'Budh',
        ],
        [
            'l' => 'Bihibar',
            'D' => 'Bihi',
        ],
        [
            'l' => 'Sukrabar',
            'D' => 'Sukra',
        ],
        [
            'l' => 'Sanibar',
            'D' => 'Sani',
        ],
    ];

    public static array $months = [
        [
            'F' => 'Baishakh',
            'M' => '',
        ],
        [
            'F' => 'Jestha',
            'M' => '',
        ],
        [
            'F' => 'Ashar',
            'M' => '',
        ],
        [
            'F' => 'Shrawan',
            'M' => '',
        ],
        [
            'F' => 'Bhadra',
            'M' => '',
        ],
        [
            'F' => 'Ashoj',
            'M' => '',
        ],
        [
            'F' => 'Kartik',
            'M' => '',
        ],
        [
            'F' => 'Mangshir',
            'M' => '',
        ],
        [
            'F' => 'Poush',
            'M' => '',
        ],
        [
            'F' => 'Magh',
            'M' => '',
        ],
        [
            'F' => 'Falgun',
            'M' => '',
        ],
        [
            'F' => 'Chaitra',
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
