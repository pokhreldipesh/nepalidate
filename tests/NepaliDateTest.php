<?php

use Dipesh\NepaliDate\NepaliDate;
use PHPUnit\Framework\TestCase;

class NepaliDateTest extends TestCase
{
    public function test_format_english()
    {
        $this->assertSame('2081, 04, , Shrawan, 25, 6, Sukra, Sukrabar, Gate', NepaliDate::make('2081-4-25')->format('Y, m, M, F, d, w, D, l, g'));
    }

    public function test_format_nepali()
    {
        $this->assertSame('२०८१, ०४, , साउन, २५, ६, शुक्र, शुक्रबार, गते', NepaliDate::make('2081-4-25')->setLang('np')->format('Y, m, M, F, d, w, D, l, g'));
    }
}
