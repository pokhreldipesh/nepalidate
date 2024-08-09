<?php

namespace Dipesh\NepaliDate\Concerns;

use Dipesh\NepaliDate\Contracts\Date;
use Exception;

trait HasDateOperation
{
    /**
     * Calculates the total number of days from a base date to the specified date.
     *
     * This method takes either a Date object or a date string, extracts the year, month, and day components,
     * and uses the daysCalculator to compute the total number of days since the base date in the Nepali calendar.
     * The result is adjusted by subtracting a constant (263) to provide the correct total.
     *
     * @param Date|string $date The target date as either a Date object or a date string.
     * @return int               The total number of days from the base date to the specified date.
     * @throws Exception         If the provided date is invalid or if any error occurs during calculation.
     */
    public function getTotalDaysFromBaseDate(Date|string $date): int
    {
        if ($date instanceof Date) {
            list($year, $month, $day) = [$date->year, $date->month, $date->day];
        } else {
            list($year, $month, $day) = $this->validateDateAndGetComponents($date);
        }
        $total = $this->daysCalculator->totalDays($year, $month, $day) - 263;
        if ($total < 0) {
            throw new \Exception("Date can not be less than 2000/09/17.");
        }
        return $total;
    }

    /**
     * Computes the difference in days between the specified date and the current instance's date.
     *
     * This method calculates the total days from the base date for both the current instance's date
     * and the provided date, returning the difference in days.
     *
     * @param Date|string $date The target date for comparison as either a Date object or a date string.
     * @return int               The difference in days between the specified date and the current instance's date.
     * @throws Exception         If the provided date is invalid or if any error occurs during calculation.
     */
    public function diffDays(Date|string $date): int
    {
        return $this->getTotalDaysFromBaseDate($date) - $this->getTotalDaysFromBaseDate($this->date);
    }

    /**
     * Retrieves the weekday of the current instance's date.
     *
     * This method calculates the day of the week for the current date using modular arithmetic,
     * returning a value from 1 (Sunday) to 7 (Saturday).
     *
     * @return int               The day of the week (1 for Sunday, 7 for Saturday).
     * @throws Exception         If any error occurs during calculation.
     */
    public function weekDay(): int
    {
        $day = $this->getTotalDaysFromBaseDate($this->date) % 7;

        return $day == 0 ? 7 : $day;
    }
}
