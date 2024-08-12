<?php

namespace Dipesh\NepaliDate\Concerns;

use Dipesh\NepaliDate\Contracts\Date;
use Exception;

trait HasDateComparison
{
    /**
     * Determine if the current date instance is equal to the specified date.
     *
     * This method compares the current instance's date with the provided date by calculating
     * the total number of days from a base date for both dates. If the number of days is the same,
     * the method returns true, indicating that the two dates are equal.
     *
     * @param  Date|string  $date  The date to compare with, either as a Date object or a date string.
     * @return bool Returns true if both dates are equal, false otherwise.
     *
     * @throws Exception If an invalid date is provided or if any error occurs during the calculation.
     */
    public function isEqual(Date|string $date): bool
    {
        return $this->getTotalDaysFromBaseDate($this->date) == $this->getTotalDaysFromBaseDate($date);
    }

    /**
     * Determine if the current date instance is greater than the specified date.
     *
     * This method compares the current instance's date with the provided date by calculating
     * the total number of days from a base date for both dates. If the current date has more
     * total days than the specified date, the method returns true.
     *
     * @param  Date|string  $date  The date to compare with, either as a Date object or a date string.
     * @return bool Returns true if the current date is greater than the specified date, false otherwise.
     *
     * @throws Exception If an invalid date is provided or if any error occurs during the calculation.
     */
    public function isGreaterThan(Date|string $date): bool
    {
        return $this->getTotalDaysFromBaseDate($this->date) > $this->getTotalDaysFromBaseDate($date);
    }

    /**
     * Determine if the current date instance is less than the specified date.
     *
     * This method compares the current instance's date with the provided date by calculating
     * the total number of days from a base date for both dates. If the current date has fewer
     * total days than the specified date, the method returns true.
     *
     * @param  Date|string  $date  The date to compare with, either as a Date object or a date string.
     * @return bool Returns true if the current date is less than the specified date, false otherwise.
     *
     * @throws Exception If an invalid date is provided or if any error occurs during the calculation.
     */
    public function isLessThan(Date|string $date): bool
    {
        return $this->getTotalDaysFromBaseDate($this->date) < $this->getTotalDaysFromBaseDate($date);
    }
}
