<?php

namespace Dipesh\NepaliDate\Concerns;

use Dipesh\NepaliDate\lang\English;
use Dipesh\NepaliDate\Services\DateOperation;
use Exception;

trait HasDate
{
    use HasCalenderLookupTable;

    public $date;
    public $day;
    public $month;
    public $year;
    protected DateOperation $calenderOperation;

    /**
     * Initialize calender
     *
     * @param string $date
     * @return void
     * @throws Exception
     */
    public function setUp(string $date): void
    {
        list(
            $this->year,
            $this->month,
            $this->day,
        ) = self::validateDateAndGetRaw($date);

        $this->date = implode("/", [$this->year, $this->month, $this->day]);

       $this->setLang(new English());

        $this->calenderOperation = new DateOperation($this);
    }

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
}
