<?php

namespace Tests;

use Dipesh\NepaliDate\NepaliDate;
use PHPUnit\Framework\TestCase;

class DateConversionTest extends TestCase
{
    public $date;

    public function setUp(): void
    {
        $this->date = new NepaliDate;
    }

    public function testToAd()
    {
        $this->assertSame((new \DateTime)->format('Y-m-d'), $this->date->toAd()->format('Y-m-d'));
    }

    public function testFromAd()
    {
        $this->assertSame($this->date->format('Y-m-d'), $this->date::fromADDate((new \DateTime)->format('Y-m-d'))->format('Y-m-d'));
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
            $this->assertEquals($bs, $this->date::fromADDate($ad)->format('Y/m/d'));
        }
    }
}
