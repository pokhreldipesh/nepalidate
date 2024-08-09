<?php

namespace Dipesh\NepaliDate\Services;

use Dipesh\NepaliDate\Contracts\Language;
use Dipesh\NepaliDate\lang\English;
use Exception;

/**
 * Class Date
 *
 * Represents a Nepali Date object, providing methods to manipulate and retrieve date components.
 */
class Date implements \Dipesh\NepaliDate\Contracts\Date
{
    /**
     * @var string $date The formatted date string (e.g., "2078/01/01").
     */
    public string $date;

    /**
     * @var int $day The day component of the date.
     */
    public int $day;

    /**
     * @var int $month The month component of the date.
     */
    public int $month;

    /**
     * @var int $year The year component of the date.
     */
    public int $year;

    /**
     * @var int $weekDay The weekday component of the date.
     */
    public int $weekDay;

    /**
     * @var Language $formattingLanguage The language used for formatting date components.
     */
    public Language $formattingLanguage;


    /**
     * Sets up the date object by parsing and validating the provided date string.
     *
     * @param string $date The date string to set up (e.g., "2078/01/01").
     * @return void
     * @throws Exception If the date format is invalid.
     */
    public function setUp(string $date): void
    {
        [$this->year, $this->month, $this->day] = self::validateDateAndGetComponents($date);
        $this->date = implode("/", [$this->year, $this->month, $this->day]);
    }

    /**
     * Validates the date string and returns its components as an array.
     *
     * @param string $date The date string to validate.
     * @return array An array containing year, month, and day.
     * @throws Exception If the date string is in an invalid format.
     */
    public static function validateDateAndGetComponents(string $date): array
    {
        preg_match_all("/\d+/", $date, $matches);

        if (count($matches[0]) < 3) {
            throw new Exception("Invalid date format. Please use 'YYYY/MM/DD'.");
        }

        return array_map('intval', $matches[0]);
    }

    /**
     * Retrieves the day component, formatted according to the language.
     *
     * @return int|string The day component, formatted in the specified language.
     */
    public function day(): int|string
    {
        return FormatDate::formatNumberToLanguage($this->day, $this->formattingLanguage);
    }

    /**
     * Retrieves the month component, formatted according to the language.
     *
     * @return int|string The month component, formatted in the specified language.
     */
    public function month(): int|string
    {
        return FormatDate::formatNumberToLanguage($this->month, $this->formattingLanguage);
    }

    /**
     * Retrieves the year component, formatted according to the language.
     *
     * @return int|string The year component, formatted in the specified language.
     */
    public function year(): int|string
    {
        return FormatDate::formatNumberToLanguage($this->year, $this->formattingLanguage);
    }
}
