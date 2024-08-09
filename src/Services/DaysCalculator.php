<?php

namespace Dipesh\NepaliDate\Services;

use Dipesh\NepaliDate\Concerns\HasCalenderLookupTable;
use Exception;

class DaysCalculator implements \Dipesh\NepaliDate\Contracts\DaysCalculator
{
    use HasCalenderLookupTable;

    /**
     * Calculates the total number of days from the beginning of the calendar up to a specified date.
     *
     * This method iterates through the years and months in the Nepali BS calendar array to sum
     * the days up to the provided year, month, and day. It provides an efficient way to convert
     * a specific date into a cumulative day count.
     *
     * @param int $year  The year component of the date.
     * @param int $month The month component of the date (1-12).
     * @param int $day   The day component of the date (1-32, depending on the month).
     * @return int       The total number of days from the beginning of the BS calendar to the given date.
     */
    public function totalDays(int $year, int $month, int $day): int
    {
        $totalDays = 0;

        foreach (self::$bs as $y => $months) {
            if ($y < $year) {
                $totalDays += array_sum($months);
            } elseif ($y == $year) {
                $totalDays += array_sum(array_slice($months, 0, $month - 1)) + $day;
                break; // Stop the loop as we've reached the target year
            }
        }

        return $totalDays;
    }

    /**
     * Adds a given number of days to the start of the Nepali BS calendar and returns the resulting date.
     *
     * This method is useful for date calculations where a specific number of days need to be added to a base date.
     * It iterates through the calendar years and months, accumulating days until the target date is reached.
     *
     * @param int $totalDays The total number of days to be added.
     * @return string        The calculated date in "YYYY/MM/DD" format.
     * @throws Exception     If the number of days exceeds the available range in the BS calendar.
     */
    public function addDays(int $totalDays): string
    {
        $accumulatedDays = 0;

        // Iterate through the years in the BS calendar
        foreach (self::$bs as $year => $months) {
            // Calculate the total number of days in the current year
            $daysInYear = array_sum($months);

            if ($totalDays <= $accumulatedDays + $daysInYear) {
                // Determine the specific month and day within the identified year
                foreach ($months as $monthIndex => $monthDays) {
                    if ($totalDays <= $accumulatedDays + $monthDays) {
                        $finalYear = $year;
                        $finalMonth = $monthIndex + 1;
                        $finalDay = $totalDays - $accumulatedDays;
                        return sprintf("%04d/%02d/%02d", $finalYear, $finalMonth, $finalDay);
                    }
                    $accumulatedDays += $monthDays;
                }
            } else {
                $accumulatedDays += $daysInYear;
            }
        }

        throw new Exception("Total days exceed the range in the BS array.");
    }
}
