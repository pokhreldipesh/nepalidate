<?php

namespace Tests;

use Dipesh\NepaliDate\NepaliDate;
use PHPUnit\Framework\TestCase;

class ComparisonTest extends TestCase
{
    public $date;

    public function setUp(): void
    {
        $this->date = new NepaliDate(date: '2081/04/24');
    }

    public function testAddDays()
    {
        $this->assertSame('2081/04/28', $this->date->addDays(4)->format('Y/m/d'));
    }

    public function testSubDays()
    {
        $this->assertSame('2081/04/20', $this->date->subDays(4)->format('Y/m/d'));
    }

    public function testIsEqual()
    {
        $this->assertTrue($this->date->isEqual('2081/04/24'));
    }

    public function testIsGreaterThan()
    {
        $this->assertFalse($this->date->isGreaterThan('2081/04/25'));
    }

    public function testIsLessThan()
    {
        $this->assertTrue($this->date->isLessThan('2081/04/25'));
    }
}
