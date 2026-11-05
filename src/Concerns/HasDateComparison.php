<?php

namespace Dipesh\NepaliDate\Concerns;

use Dipesh\NepaliDate\Contracts\Date;
use Dipesh\NepaliDate\Services\Date as ServicesDate;
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

    /**
     * Determine if the current date is the anniversary of the specified date.
     *
     * An anniversary shares the same month and day, regardless of year. A date is
     * therefore its own anniversary. Components are compared directly, so no
     * calendar arithmetic is involved and BS month lengths do not matter.
     *
     * When $date is omitted (or null), today's date — from NepaliDate::now() in the
     * current calendar — is used as the reference date.
     *
     * @param  Date|string|null  $date  The reference date (Date object or date string). Defaults to today.
     * @return bool Returns true if both dates share the same month and day, false otherwise.
     *
     * @throws Exception If an invalid date is provided or if any error occurs during parsing.
     */
    public function isAnniversary(Date|string|null $date = null): bool
    {
        $date ??= static::now($this->dataSet);

        [, $month, $day] = $date instanceof Date
            ? [$date->getYear(), $date->getMonth(), $date->getDay()]
            : ServicesDate::parseComponents($date);

        return $this->month === $month && $this->day === $day;
    }

    /**
     * Determine if the current date is the birthday of the specified date.
     *
     * A convenience alias for {@see isAnniversary()} — a birthday is simply the
     * anniversary of a birth date (same month and day, regardless of year).
     *
     * @param  Date|string|null  $date  The reference date (Date object or date string). Defaults to today.
     * @return bool Returns true if both dates share the same month and day, false otherwise.
     *
     * @throws Exception If an invalid date is provided or if any error occurs during parsing.
     */
    public function isBirthday(Date|string|null $date = null): bool
    {
        return $this->isAnniversary($date);
    }
}
