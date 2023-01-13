<?php

namespace Tests;

use Dipesh\NepaliDate\NepaliDate;
use PHPUnit\Framework\TestCase;

class ComparisionTest extends TestCase
{
    public $date;

    public function setUp(): void
    {
        $this->date = (new NepaliDate('1994/01/21'))->toNepali();
    }

    public function testIsEqual()
    {
        $newDate = $this->date->isEqual('2050/10/8');

        $this->assertTrue($newDate);
    }

    public function testIsGreaterThan()
    {
        $newDate = $this->date->isGreaterThan('2050/10/8');

        $this->assertFalse($newDate);
    }

    public function testIsGreaterThanOrEqual()
    {
        $newDate = $this->date->isGreaterThanOrEqual('2050/10/8');

        $this->assertTrue($newDate);
    }

    public function testIsLessThan()
    {
        $newDate = $this->date->isLessThan('2050/10/8');

        $this->assertFalse($newDate);
    }

    public function testIsLessThanOrEqual()
    {
        $newDate = $this->date->isLessThanOrEqual('2050/10/8');

        $this->assertTrue($newDate);
    }
}
