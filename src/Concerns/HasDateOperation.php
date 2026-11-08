<?php

declare(strict_types=1);

namespace Dipesh\NepaliDate\Concerns;

use Dipesh\Date\Date;
use Dipesh\NepaliDate\NepaliDate;
use Dipesh\NepaliDate\SystemDataSet;
use Exception;

trait HasDateOperation
{
    /**
     * Calculate the total number of days from the dataset's base Nepali date.
     *
     * Returns 0 on the base date, positive after, negative before.
     *
     * @param  NepaliDate|Date|string  $date  Date object or date string.
     * @return int Days from the base date.
     *
     * @throws Exception If the date string is invalid.
     */
    public function getTotalDaysFromBaseDate(NepaliDate|Date|string $date): int
    {
        [$year, $month, $day] = $this->componentsOf($date);

        [$baseYear, $baseMonth, $baseDay] = Date::parseComponents(
            $this->dataSet?->getEquivalentNepaliDate()
                ?? SystemDataSet::DEFAULT_EQUIVALENT_NEPALI_DATE
        );

        return $this->dateProcessor->getDays($year, $month, $day)
            - $this->dateProcessor->getDays($baseYear, $baseMonth, $baseDay);
    }

    /**
     * Compute the difference in days between the given date and the current date.
     *
     * @param  NepaliDate|Date|string  $date  Target date.
     * @return int Difference in days (positive if $date is later).
     *
     * @throws Exception If the date string is invalid.
     */
    public function diffDays(NepaliDate|Date|string $date): int
    {
        return $this->getTotalDaysFromBaseDate($date) - $this->getTotalDaysFromBaseDate($this->date);
    }

    /**
     * Get the weekday of the current date in the requested format.
     *
     * @param  string  $format  'w' for number, 'D' for short name, 'l' for full name.
     *
     * @throws Exception If the format is unsupported.
     */
    public function weekDay(string $format = 'w'): int|string
    {
        return $this->date->getFormatter()->formatWeekDay($format);
    }

    /**
     * Reduce a date reference to its year, month, and day.
     *
     * @return array{0: int, 1: int, 2: int} Year, month, day.
     *
     * @throws Exception If the date string is invalid.
     */
    private function componentsOf(NepaliDate|Date|string $date): array
    {
        $system = $date instanceof NepaliDate ? $date->date : $date;

        return is_string($system)
            ? Date::parseComponents($system)
            : [$system->getYear(), $system->getMonth(), $system->getDay()];
    }
}
