<?php

declare(strict_types=1);

namespace Dipesh\NepaliDate;

use DateInterval;
use DateTime;
use DateTimeInterface;
use DateTimeZone;
use Exception;

/**
 * Lightweight English/AD date helper.
 *
 * Provides immutable day/month/year arithmetic on top of \DateTime.
 * Each manipulation method returns a new instance — the original is unchanged.
 */
class EnDate extends DateTime
{
    /**
     * @param  string  $time  Date/time string. Defaults to 'now'.
     * @param  string  $timezone  Timezone identifier. Defaults to 'Asia/Kathmandu'.
     *
     * @throws Exception
     */
    public function __construct(string $time = 'now', string $timezone = 'Asia/Kathmandu')
    {
        parent::__construct($time, new DateTimeZone($timezone));
    }

    /**
     * Return a new instance with the given number of days added.
     */
    public function addDays(int $days): static
    {
        $clone = clone $this;
        $clone->add(new DateInterval("P{$days}D"));

        return $clone;
    }

    /**
     * Return a new instance with the given number of days subtracted.
     */
    public function subDays(int $days): static
    {
        return $this->addDays(-$days);
    }

    /**
     * Return a new instance with the given number of months added.
     */
    public function addMonths(int $months): static
    {
        $clone = clone $this;
        $clone->add(new DateInterval("P{$months}M"));

        return $clone;
    }

    /**
     * Return a new instance with the given number of months subtracted.
     */
    public function subMonths(int $months): static
    {
        return $this->addMonths(-$months);
    }

    /**
     * Return a new instance with the given number of years added.
     */
    public function addYears(int $years): static
    {
        $clone = clone $this;
        $clone->add(new DateInterval("P{$years}Y"));

        return $clone;
    }

    /**
     * Return a new instance with the given number of years subtracted.
     */
    public function subYears(int $years): static
    {
        return $this->addYears(-$years);
    }

    /**
     * Absolute difference in days between this date and another.
     */
    public function diffDays(DateTimeInterface $date): int
    {
        return (int) $this->diff($date)->days;
    }
}
