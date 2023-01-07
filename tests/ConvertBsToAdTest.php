<?php

namespace Tests;

use Dipesh\NepaliDate\NepaliDate;
use PHPUnit\Framework\TestCase;

class ConvertBsToAdTest extends TestCase
{
    private $date;

    public function setUp(): void
    {
        $this->date = new NepaliDate();
    }

    public function testConvertBsToAd()
    {
        $this->date->toEnglish('2050-10-08');

        $this->assertIsObject($this->date);
    }
}
