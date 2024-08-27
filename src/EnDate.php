<?php

namespace Dipesh\NepaliDate;

use DateInterval;
use DateTimeInterface;
use DateTimeZone;
use Exception;

/**
 * EnDate class extends the built-in DateTime class and provides additional methods
 * to perform date manipulation such as adding/subtracting days, months, and years.
 * It also allows calculating the difference in days between two dates.
 */
class EnDate extends \DateTime
{
    /**
     * EnDate constructor.
     *
     * Initializes a new EnDate instance with the specified time and timezone.
     * If no time is provided, the current date and time are used.
     *
     * @param  string  $time  The date/time string. Default is 'now'.
     * @param  string  $timezone  The timezone identifier. Default is 'Asia/Kathmandu'.
     *
     * @throws Exception
     */
    public function __construct(string $time = 'now', $timezone = 'Asia/Kathmandu')
    {
        parent::__construct($time, new DateTimeZone($timezone));
    }

    /**
     * Adds a specified number of days to the current date.
     *
     * @param  int  $days  The number of days to add.
     * @return $this The current instance with the modified date.
     */
    public function addDays(int $days): static
    {
        $interval = new DateInterval("P{$days}D");
        $this->add($interval);

        return $this;
    }

    /**
     * Subtracts a specified number of days from the current date.
     *
     * @param  int  $days  The number of days to subtract.
     * @return $this The current instance with the modified date.
     */
    public function subDays(int $days): static
    {
        $interval = new DateInterval("P{$days}D");
        $this->sub($interval);

        return $this;
    }

    /**
     * Adds a specified number of months to the current date.
     *
     * @param  int  $months  The number of months to add.
     * @return $this The current instance with the modified date.
     */
    public function addMonths(int $months): static
    {
        $interval = new DateInterval("P{$months}M");
        $this->add($interval);

        return $this;
    }

    /**
     * Subtracts a specified number of months from the current date.
     *
     * @param  int  $months  The number of months to subtract.
     * @return $this The current instance with the modified date.
     */
    public function subMonths(int $months): static
    {
        $interval = new DateInterval("P{$months}M");
        $this->sub($interval);

        return $this;
    }

    /**
     * Adds a specified number of years to the current date.
     *
     * @param  int  $years  The number of years to add.
     * @return $this The current instance with the modified date.
     */
    public function addYears(int $years): static
    {
        $interval = new DateInterval("P{$years}Y");
        $this->add($interval);

        return $this;
    }

    /**
     * Subtracts a specified number of years from the current date.
     *
     * @param  int  $years  The number of years to subtract.
     * @return $this The current instance with the modified date.
     */
    public function subYears(int $years): static
    {
        $interval = new DateInterval("P{$years}Y");
        $this->sub($interval);

        return $this;
    }

    /**
     * Calculates the difference in days between the current date and another date.
     *
     * @param  DateTimeInterface  $date  The date to compare with.
     * @return false|int The number of days between the two dates, or false on failure.
     */
    public function diffDays(DateTimeInterface $date): false|int
    {
        $interval = $this->diff($date);

        return $interval->days;
    }

    /**
     * Formats the date according to the specified format.
     *
     * @param  string  $format  The format string.
     * @return string The formatted date string.
     */
    public function format(string $format): string
    {
        return parent::format($format);
    }
}
