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

    public function testConvertDate()
    {
        $this->date->toNepali(date('Y/m/d'));

        $this->assertIsString($this->date->__toString());
    }

    public function testConvertDateFor4Years()
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

    public function testPerformTruthValidation()
    {
        $dates = [
            '1944/1/1' => '2000/9/17',
            '1944/10/7' => '2001/6/22',
            '1994/1/21' => '2050/10/8',
            '2001/7/27' => '2058/4/12',
            '2023/1/7' => '2079/9/23',
        ];

        foreach ($dates as $ad => $bs) {
            $this->assertEquals($bs, $this->date->toNepali($ad)->format('Y/m/d'));
        }
    }

    public function testDays()
    {
        $this->date->toNepali(date('Y/m/d'));

        $this->assertIsInt($this->date->day());
    }

    public function testMonth()
    {
        $this->date->toNepali(date('Y/m/d'));

        $this->assertIsInt($this->date->month());
    }

    public function testYear()
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
