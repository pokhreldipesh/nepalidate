<?php

namespace Dipesh\NepaliDate\Concerns;

use Carbon\Carbon;
use Dipesh\NepaliDate\Services\FormatDate;
use Exception;

trait HasDate
{
    use HasCalenderLookupTable;

    /**
     * Get formatted date string for calender
     *
     * @param array $date
     * @return string
     */
    public static function getFormattedDateString(array $date): string
    {
        return implode("/", $date);
    }

    /**
     * Validate date and get array date format for date operation
     *
     * @param string $date
     * @return array
     * @throws Exception
     */
    public static function validateDateAndGetRaw(string $date) : array
    {
        preg_match_all("/\d*/m", $date, $matches);

        if (count(array_filter($matches[0])) < 3) {
            throw new Exception("Invalid date format.");
        }

        return array_values(array_filter($matches[0]));
    }

    /**
     * Get current date
     *
     * @return $this
     * @throws Exception
     */
    public static function now(): static
    {
        return self::fromADDate(Carbon::now()->format("Y-m-d"));
    }
    /**
     * Convert calender date to AD
     *
     * @return Carbon
     * @throws Exception
     */
    public function toAd(): Carbon
    {
        return Carbon::parse(self::$baseEnglishDate)->addDays(
            $this->calenderOperation->getTotalDaysFromBaseDate($this->date)
        );
    }

    /**
     * Convert date from AD to BS
     *
     * @param string $date
     * @return static
     * @throws Exception
     */
    public static function fromADDate(string $date): static
    {
        return (new static(self::$equivalentNepaliDate))->addDays(Carbon::parse(self::$baseEnglishDate)->diffInDays($date));
    }

    /**
     * Get day from date
     *
     * @return int|string
     */
    public function day(): int|string
    {
        return FormatDate::formatNumberToLanguage($this->day, $this->formattingLanguage);
    }

    /**
     * Get month from date
     *
     * @return int|string
     */
    public function month(): int|string
    {
        return FormatDate::formatNumberToLanguage($this->month, $this->formattingLanguage);
    }

    /**
     * Get year from date
     *
     * @return int|string
     */
    public function year(): int|string
    {
        return FormatDate::formatNumberToLanguage($this->year, $this->formattingLanguage);
    }
}
