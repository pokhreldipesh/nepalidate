<?php

namespace Dipesh\NepaliDate\Contracts;

interface DateProcessor
{
    public function getDays(int $year, int $month, int $day):int;
    public function getDateFromDays(int $totalDays): string;
    public function getWeekDayFromDays(int $days):int;
}
