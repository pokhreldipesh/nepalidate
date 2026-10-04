<?php

namespace Dipesh\NepaliDate\Concerns;

use Dipesh\NepaliDate\Contracts\Date;
use Dipesh\NepaliDate\Services\Date as ServicesDate;
use Dipesh\NepaliDate\SystemDataSet;
use Exception;

trait HasDateOperation
{
    /**
     * Calculate the total number of days from the dataset's base Nepali date.
     *
     * Returns 0 on the base date, positive after, negative before.
     *
     * @param  Date|string  $date  Date object or date string.
     * @return int Days from the base date.
     *
     * @throws Exception If the date string is invalid.
     */
    public function getTotalDaysFromBaseDate(Date|string $date): int
    {
        [$year, $month, $day] = $date instanceof Date
            ? [$date->year, $date->month, $date->day]
            : ServicesDate::parseComponents($date);

        [$baseYear, $baseMonth, $baseDay] = ServicesDate::parseComponents(
            $this->dataSet?->getEquivalentNepaliDate()
                ?? SystemDataSet::DEFAULT_EQUIVALENT_NEPALI_DATE
        );

        return $this->dateProcessor->getDays($year, $month, $day)
            - $this->dateProcessor->getDays($baseYear, $baseMonth, $baseDay);
    }

    /**
     * Compute the difference in days between the given date and the current date.
     *
     * @param  Date|string  $date  Target date.
     * @return int Difference in days (positive if $date is later).
     *
     * @throws Exception If the date string is invalid.
     */
    public function diffDays(Date|string $date): int
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
        return $this->formatter->formatWeekDay($format);
    }
}
