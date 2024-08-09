<?php

use PHPUnit\Framework\TestCase;

class NepaliDateTest extends TestCase
{
    public function testFormatEnglish()
    {
        $this->assertSame("2081, 4, Shrawan, Shrawan, 25, 6, Sukra, Sukrabar, Gate", \Dipesh\NepaliDate\NepaliDate::make("2081-4-25")->format("Y, m, M, F, d, w, D, l, g"));
    }

    public function testFormatNepali()
    {
        $this->assertSame("२०८१, ४, श्रावन, श्रावन, २५, ६, शुक्र, शुक्रबार, गते", \Dipesh\NepaliDate\NepaliDate::make("2081-4-25")->setLang('np')->format("Y, m, M, F, d, w, D, l, g"));
    }
}
