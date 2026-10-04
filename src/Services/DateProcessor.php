<?php

namespace Dipesh\NepaliDate\Services;

use Dipesh\NepaliDate\DataSet;
use Dipesh\NepaliDate\InvalidDateRangeException;
use Dipesh\NepaliDate\SystemDataSet;
use Exception;

class DateProcessor implements \Dipesh\NepaliDate\Contracts\DateProcessor
{
    private const BASE_WEEK_DAY = 7;

    /**
     * Optional custom dataset. When null, the packaged calendar is used.
     */
    private ?DataSet $dataSet;

    private ?DataSet $defaultDataSet = null;

    public function __construct(?DataSet $dataSet = null)
    {
        $this->dataSet = $dataSet;
    }

    public function getDataSet(): ?DataSet
    {
        return $this->dataSet;
    }

    private function calendar(): DataSet
    {
        return $this->dataSet ?? ($this->defaultDataSet ??= SystemDataSet::packaged());
    }

    /**
     * @return array<int, array<int, int>>
     */
    private function calendarRows(): array
    {
        return iterator_to_array($this->calendar());
    }

    private function baseWeekDay(): int
    {
        return self::BASE_WEEK_DAY;
    }

    private function equivalentNepaliDate(): string
    {
        return $this->calendar()->getEquivalentNepaliDate();
    }

    /**
     * Calculates the total number of days from the beginning of the calendar up to a specified date.
     *
     * This method iterates through the years and months in the Nepali BS calendar array to sum
     * the days up to the provided year, month, and day. It provides an efficient way to convert
     * a specific date into a cumulative day count.
     *
     * @param  int  $year  The year component of the date.
     * @param  int  $month  The month component of the date (1-12).
     * @param  int  $day  The day component of the date (1-32, depending on the month).
     * @return int The total number of days from the beginning of the BS calendar to the given date.
     *
     * @throws Exception
     */
    public function getDays(int $year, int $month, int $day): int
    {
        $bs = $this->calendarRows();
        $totalDays = 0;

        if (! isset($bs[$year])) {
            throw new InvalidDateRangeException;
        }

        foreach ($bs as $y => $months) {
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
     * Days from the dataset's equivalent Nepali base date (0 on that date).
     *
     * @internal Use getDays() arithmetic instead. Kept public for backward compatibility with existing tests.
     */
    public function getDaysFromBase(int $year, int $month, int $day): int
    {
        [$baseYear, $baseMonth, $baseDay] = $this->parseYmd($this->equivalentNepaliDate());

        return $this->getDays($year, $month, $day) - $this->getDays($baseYear, $baseMonth, $baseDay);
    }

    /**
     * Calculates a date in the Nepali BS calendar based on a given number of days.
     *
     * This method is useful for date calculations where a specific number of days need to be added to a base date.
     * It iterates through the calendar years and months, accumulating days until the target date is reached.
     *
     * @param  int  $totalDays  The total number of days to be added.
     * @return string The calculated date in "YYYY/MM/DD" format.
     *
     * @throws Exception If the number of days exceeds the available range in the BS calendar.
     */
    public function getDateFromDays(int $totalDays): string
    {
        $bs = $this->calendarRows();
        $accumulatedDays = 0;

        // Iterate through the years in the BS calendar
        foreach ($bs as $year => $months) {
            // Calculate the total number of days in the current year
            $daysInYear = array_sum($months);

            if ($totalDays <= $accumulatedDays + $daysInYear) {
                // Determine the specific month and day within the identified year
                foreach ($months as $monthIndex => $monthDays) {
                    if ($totalDays <= $accumulatedDays + $monthDays) {
                        $finalYear = $year;
                        $finalMonth = $monthIndex + 1;
                        $finalDay = $totalDays - $accumulatedDays;

                        return sprintf(Date::$defaultOutputFormat, $finalYear, $finalMonth, $finalDay);
                    }
                    $accumulatedDays += $monthDays;
                }
            } else {
                $accumulatedDays += $daysInYear;
            }
        }

        throw new InvalidDateRangeException;
    }

    /**
     * Calculate the corresponding weekday for a given number of days.
     *
     * This method calculates the weekday by taking the modulus of the number of days
     * with the base weekday. If the result is negative, it adjusts the day to ensure
     * it falls within the valid range of weekdays (1-7). The return value corresponds
     * to the day of the week, where 1 represents Sunday and 7 represents Saturday.
     *
     * @param  int  $days  The number of days to calculate the weekday for.
     * @return int The weekday corresponding to the given number of days (1 for Sunday, 7 for Saturday).
     */
    public function getWeekDayFromDays(int $days): int
    {
        $day = $days % $this->baseWeekDay();

        // Calculate weekday traversing backward from base date
        if ($day < 0) {
            $day = $day + 7;
        }

        return $day == 0 ? 7 : $day;
    }

    /**
     * @return array{0: int, 1: int, 2: int}
     */
    private function parseYmd(string $date): array
    {
        [$year, $month, $day] = array_map('intval', explode('/', $date));

        return [$year, $month, $day];
    }
}
