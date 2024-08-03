<?php

namespace Dipesh\NepaliDate\Services;

use Dipesh\NepaliDate\Concerns\HasCalenderLookupTable;
use Dipesh\NepaliDate\Concerns\HasDateComparison;
use Dipesh\NepaliDate\Contracts\Date;
use Dipesh\NepaliDate\Contracts\DateOperations;
use Dipesh\NepaliDate\NepaliDate;
use Exception;

class DateOperation implements DateOperations
{
    use HasCalenderLookupTable, HasDateComparison;

    private Date $date;

    /**
     * @param Date $date
     */
    public function __construct(Date $date)
    {
        $this->date = $date;
    }

    /**
     * Count total days from starting
     *
     * @param int $year
     * @param int $month
     * @param int $day
     * @return int
     * @throws Exception
     */
    public function total(int $year, int $month, int $day): int
    {
        $totalDays = 0;

        foreach (self::$bs as $y => $months) {
            if ($y < $year) {
                $totalDays += array_sum($months);
            } elseif ($y == $year) {
                $totalDays += array_sum(array_slice($months, 0, $month - 1)) + $day;
                break; // No need to continue the loop once we've reached the target year
            }
        }

        return $totalDays;
    }

    /**
     * @throws Exception
     */
    public function getTotalDaysFromBaseDate(Date|string $date): int
    {
        if ($date instanceof Date) {
            list($year, $month, $day) = [$date->year, $date->month, $date->day];
        } else {
            list($year, $month, $day) = NepaliDate::validateDateAndGetRaw($date);
        }
        return $this->total($year, $month, $day) - 263;
    }

    /**
     * @param int $day
     *
     * @return Date
     * @throws Exception
     */
    public function addDays(int $day): string
    {
        $totalDays = $this->total($this->date->year, $this->date->month, $this->date->day) + $day;
        $accumulatedDays = 0;
        $finalYear = 0;
        $finalMonth = 0;
        $finalDay = 0;

        foreach (self::$bs as $year => $months) {
            foreach ($months as $monthIndex => $monthDays) {
                $accumulatedDays += $monthDays;
                if ($accumulatedDays >= $totalDays) {
                    $finalYear = $year;
                    $finalMonth = $monthIndex + 1;
                    $finalDay = $monthDays - ($accumulatedDays - $totalDays);
                    break 2; // Break both foreach loops
                }
            }
        }
        return sprintf("%s/%s/%s", $finalYear, $finalMonth, $finalDay);
    }

    /**
     * @param $date
     * @return int
     * @throws Exception
     */
    public function diffDays($date): int
    {
        return $this->getTotalDaysFromBaseDate($date) - $this->getTotalDaysFromBaseDate($this->date);
    }

    /**
     * Get week day of calendar date
     *
     * @return int
     * @throws Exception
     */
    public function weekDay(): int
    {
        $day = $this->getTotalDaysFromBaseDate($this->date) % 7;

        return  $day == 0 ? 7 : $day;
    }
}
