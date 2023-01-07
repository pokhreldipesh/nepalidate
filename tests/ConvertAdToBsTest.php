<?php

namespace Tests;

use Dipesh\NepaliDate\NepaliDate;
use PHPUnit\Framework\TestCase;

class ConvertAdToBsTest extends TestCase
{
    public $date;

    public function setUp(): void
    {
        $this->date = new NepaliDate();
    }

    public function testConvertAdToBs()
    {
        $this->date->toNepali(date('Y/m/d'));

        $this->assertIsString($this->date->__toString());
    }

    public function testConvertAdToBsFor4Years()
    {
        $totalDaysToTest = 365 * 4;

        $convertedDate = [];

        while ($totalDaysToTest >= 1) {
            $this->date->toNepali(date_create('2018/01/01')->add(date_interval_create_from_date_string("$totalDaysToTest days"))->format('Y/m/d'));
            $convertedDate[] = $this->date;

            --$totalDaysToTest;
        }

        $this->assertIsArray($convertedDate);
    }

    public function testBsDays()
    {
        $this->date->toNepali(date('Y/m/d'));

        $this->assertIsInt($this->date->day());
    }

    public function testBsMonth()
    {
        $this->date->toNepali(date('Y/m/d'));

        $this->assertIsInt($this->date->month());
    }

    public function testBsYear()
    {
        $this->date->toNepali(date('Y/m/d'));

        $this->assertIsInt($this->date->year());
    }

    public function testWeekDay()
    {
        $this->date->toNepali(date('Y/m/d'));

        $this->assertIsInt($this->date->weekDay());
    }

    public function testLang()
    {
        $date1 = $this->date->toNepali(date('Y/m/d'))->lang('np')->format('Y/m/d');
        $date2 = $this->date->toNepali(date('Y/m/d'))->lang('en')->format('Y/m/d');

        $this->assertIsString($date1);
        $this->assertIsString($date2);
    }

    public function testDateFormat()
    {
        $date = $this->date->toNepali(date('Y/m/d'))->format('Y m d M F w D l');

        $this->assertIsString($date);
    }
}
