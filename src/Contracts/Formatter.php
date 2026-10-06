<?php

declare(strict_types=1);

namespace Dipesh\NepaliDate\Contracts;

use Exception;

interface Formatter
{
    /**
     * Format the date according to a format string.
     *
     * @throws Exception If the format is invalid.
     */
    public function format(string $format): string;

    /**
     * Convert a number to language-specific digits.
     */
    public function formatNumber(int|string $number): string;

    /**
     * Format the month component.
     *
     * @param  string  $format  'm' for zero-padded number, 'M' for short name, 'F' for full name.
     */
    public function formatMonth(string $format = 'm'): mixed;

    /**
     * Format the weekday component.
     *
     * @param  string  $format  'w' for number, 'D' for short name, 'l' for full name.
     */
    public function formatWeekDay(string $format = 'w'): mixed;
}
