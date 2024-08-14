<?php

namespace Tests;

use Dipesh\NepaliDate\NepaliDate;
use PHPUnit\Framework\Attributes\DataProvider;
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

    #[DataProvider('ADToBSDates')]
    public function testDateConversionFromADToBS(string $AD, string $BS ): void
    {
        $this->assertEquals($BS, $this->date::fromADDate($AD)->format('Y/m/d'));
    }

    public static function ADToBSDates(): array
    {
        return [
            ['AD' => '1944/1/1', 'BS' => '2000/9/17'],
            ['AD' => '1944/10/7', 'BS' => '2001/6/22'],
            ['AD' => '1994/1/21', 'BS' => '2050/10/8'],
            ['AD' => '2001/7/27', 'BS' => '2058/4/12'],
            ['AD' => '2023/1/7', 'BS' => '2079/9/23'],
        ];
    }
}
