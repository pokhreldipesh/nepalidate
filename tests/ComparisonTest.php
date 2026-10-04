<?php

namespace Tests;

use Dipesh\NepaliDate\NepaliDate;
use PHPUnit\Framework\TestCase;

class ComparisonTest extends TestCase
{
    public $date;

    protected function setUp(): void
    {
        $this->date = new NepaliDate(date: '2081/04/24');
    }

    public function test_add_days()
    {
        $this->assertSame('2081/04/28', $this->date->addDays(4)->format('Y/m/d'));
    }

    public function test_sub_days()
    {
        $this->assertSame('2081/04/20', $this->date->subDays(4)->format('Y/m/d'));
    }

    public function test_is_equal()
    {
        $this->assertTrue($this->date->isEqual('2081/04/24'));
    }

    public function test_is_greater_than()
    {
        $this->assertFalse($this->date->isGreaterThan('2081/04/25'));
    }

    public function test_is_less_than()
    {
        $this->assertTrue($this->date->isLessThan('2081/04/25'));
    }
}
