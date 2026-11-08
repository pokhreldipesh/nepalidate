<?php

namespace Dipesh\NepaliDate\Concerns;

use Dipesh\NepaliDate\DataSet;
use Dipesh\NepaliDate\EnDate;
use Dipesh\NepaliDate\SystemDataSet;
use Exception;

trait HasDateConversion
{
    /**
     * Convert the current Nepali date instance to its equivalent AD (Gregorian) date.
     *
     * This method calculates the equivalent AD date by adding the total number of days
     * from a base AD date to the current Nepali date. The result is returned as a Carbon instance.
     *
     * @return EnDate Returns a Carbon instance representing the equivalent AD date.
     *
     * @throws Exception If an error occurs during the conversion process.
     */
    public function toAd(): EnDate
    {
        $baseEnglishDate = $this->dataSet?->getBaseEnglishDate() ?? SystemDataSet::DEFAULT_BASE_ENGLISH_DATE;

        return new EnDate($baseEnglishDate)->addDays(
            $this->getTotalDaysFromBaseDate($this->date)
        );
    }

    /**
     * Convert a given AD (Gregorian) date to its equivalent Nepali date (BS).
     *
     * This static method takes an AD date string and calculates its equivalent Nepali date (BS)
     * by determining the difference in days from a base AD date and applying this to the
     * base Nepali date. The resulting Nepali date is returned as an instance of the calling class.
     *
     * @param  string  $date  The AD date to be converted, provided as a string.
     * @param  DataSet|null  $dataSet  Optional custom calendar dataset; defaults to the packaged calendar.
     * @return static Returns an instance of the calling class representing the equivalent Nepali date.
     *
     * @throws Exception If an invalid date is provided or if any error occurs during the conversion.
     */
    public static function fromADDate(string $date, ?DataSet $dataSet = null): static
    {
        $baseEnglishDate = $dataSet?->getBaseEnglishDate() ?? SystemDataSet::DEFAULT_BASE_ENGLISH_DATE;
        $equivalentNepaliDate = $dataSet?->getEquivalentNepaliDate() ?? SystemDataSet::DEFAULT_EQUIVALENT_NEPALI_DATE;

        $instance = new static($equivalentNepaliDate, null, $dataSet);

        return $instance->addDays(
            new EnDate($baseEnglishDate)->diffDays(new EnDate($date))
        );
    }
}
