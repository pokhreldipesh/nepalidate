<?php

namespace Dipesh\NepaliDate\Contracts;

interface DaysCalculator
{
    public function totalDays(int $year, int $month, int $day):int;
    public function addDays(int $totalDays): string;
    public function weekDay(int $days):int;
}
