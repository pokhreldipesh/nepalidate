<?php

namespace Dipesh\NepaliDate\Services;

use Exception;

/**
 * Default formatter for BS (Bikram Sambat) dates.
 *
 * Reads components live from the Date passed at construction.
 * Supported format characters: Y, m, M, F, d, w, D, l, g.
 */
class FormatDate extends Formatter
{
    /**
     * @var string[] Format characters that render a weekday.
     */
    private const WEEKDAY_FORMATS = ['w', 'D', 'l'];

    /**
     * @var string[] Format characters that render a month.
     */
    private const MONTH_FORMATS = ['m', 'M', 'F'];

    /**
     * Format the date according to the provided format string.
     *
     * @param  string  $format  Format string (e.g. 'Y/m/d').
     *
     * @throws Exception If the format contains unsupported characters.
     */
    public function format(string $format): string
    {
        $this->validateSupportedFormats($format);

        return (string) preg_replace_callback('/\w*/m', function ($matches): string {
            $char = $matches[0];

            return ($char && in_array($char, $this->supportedFormats))
                ? $this->processFormatChar($char)
                : '';
        }, $format);
    }

    /**
     * Format the month component.
     *
     * @param  string  $format  'm' for zero-padded number, 'M' for short name, 'F' for full name.
     *
     * @throws Exception If the format is unsupported.
     */
    public function formatMonth(string $format = 'm'): mixed
    {
        if (! in_array($format, self::MONTH_FORMATS)) {
            throw new Exception('Unsupported month format. Please use "m", "M", or "F".');
        }

        if (in_array($format, ['M', 'F'])) {
            return $this->language()->getMonth($this->date->getMonth() - 1)[$format];
        }

        return $this->formatNumber(sprintf('%02d', $this->date->getMonth()));
    }

    /**
     * Format the weekday component.
     *
     * @param  string  $format  'w' for number, 'D' for short name, 'l' for full name.
     *
     * @throws Exception If the format is unsupported.
     */
    public function formatWeekDay(string $format = 'w'): mixed
    {
        if (! in_array($format, self::WEEKDAY_FORMATS)) {
            throw new Exception('Unsupported day format. Please use "w", "D", or "l".');
        }

        if (in_array($format, ['D', 'l'])) {
            return $this->language()->getWeek($this->date->getWeekDay() - 1)[$format];
        }

        return $this->formatNumber($this->date->getWeekDay());
    }

    /**
     * Map a single format character to its rendered string.
     */
    private function processFormatChar(string $char): string
    {
        return match (true) {
            in_array($char, self::WEEKDAY_FORMATS) => $this->formatWeekDay($char),
            in_array($char, self::MONTH_FORMATS) => $this->formatMonth($char),
            $char === 'g' => $this->language()->getGate(),
            $char === 'd' => $this->formatNumber(sprintf('%02d', $this->date->getDay())),
            default => $this->formatNumber($this->date->getYear()), // 'Y'
        };
    }
}
