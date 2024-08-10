<?php

namespace Dipesh\NepaliDate\Services;

use Dipesh\NepaliDate\Contracts\Date as DateAlias;
use Dipesh\NepaliDate\Contracts\Formatter;
use Dipesh\NepaliDate\Contracts\Language;
use Exception;

/**
 * Class FormatDate
 *
 * Handles the formatting of Nepali dates based on provided formats and language settings.
 */
class FormatDate implements Formatter
{
    /**
     * @var array $date Stores the date components like year, month, day, and weekday.
     */
    private array $date = ['Y', 'm', 'd', 'w'];

    /**
     * @var array $supportedFormats List of supported date format characters.
     */
    protected array $supportedFormats = ['Y', 'm', 'M', 'F', 'd', 'w', 'D', 'l', 'g'];

    /**
     * @var Language $defaultLang The language used for formatting.
     */
    protected Language $defaultLang;

    /**
     * @throws Exception
     */
    public function __invoke(string $format, DateAlias $date, Language $lang): string
    {
        $this->setUp($date, $lang);

        return $this->format($format);
    }

    /**
     * Set up the formatter with a specific date and language.
     *
     * @param Date $date
     * @param Language $lang The language to use for formatting.
     * @return static
     */
    public function setUp(DateAlias $date, Language $lang): static
    {
        $this->defaultLang = $lang;

        $this->date = array_combine($this->date, [
            $date->year,
            $date->month,
            $date->day,
            fn()=> $date->weekDay()
        ]);

        return $this;
    }

    /**
     * Formats the date according to the provided format string.
     *
     * @param string $format The format string (e.g., 'Y/m/d').
     * @return string
     * @throws Exception If the format string contains unsupported characters.
     */
    public function format(string $format = 'Y/m/d'): string
    {
        $this->validateSupportedFormats($format);

        return preg_replace_callback("/\w*/m", function ($matches) {
            $formatChar = $matches[0];

            if ($formatChar && in_array($formatChar, $this->supportedFormats)) {
                return $this->processFormatChar($formatChar);
            }

            return null;
        }, $format);
    }

    /**
     * Converts numbers to the appropriate language-specific digits.
     *
     * @param int $number The number to convert.
     * @param Language $language The language to use for conversion.
     * @return string
     */
    public function formatNumber(int $number, Language $language): string
    {
        return preg_replace_callback("/\d/m", function ($matches) use ($language) {
            return $language->getDigit($matches[0]);
        }, (string)$number);
    }

    /**
     * Validates if the provided format string contains only supported formats.
     *
     * @param string $format The format string to validate.
     * @throws Exception If the format string contains unsupported formats.
     */
    private function validateSupportedFormats(string $format): void
    {
        preg_match_all('/\w*/m', $format, $matches);

        $formatsInString = array_filter($matches[0]);
        $unsupportedFormats = array_diff($formatsInString, $this->supportedFormats);

        if (!empty($unsupportedFormats)) {
            throw new Exception('Invalid date format');
        }
    }

    /**
     * Formats the month according to the provided format character.
     *
     * @param string $format The month format character ('m', 'M', or 'F').
     * @return mixed
     * @throws Exception If the provided month format is not supported.
     */
    public function formatMonth(string $format = 'm'): mixed
    {
        $supportedMonthFormats = ['m', 'M', 'F'];

        if (!in_array($format, $supportedMonthFormats)) {
            throw new Exception('Unsupported month format. Please use "m", "M", or "F".');
        }

        if (in_array($format, ['M', 'F'])) {
            return $this->defaultLang->getMonth($this->date['m'] - 1);
        }

        return $this->date[$format];
    }

    /**
     * Formats the weekday according to the provided format character.
     *
     * @param string $format The weekday format character ('w', 'D', or 'l').
     * @return mixed
     * @throws Exception If the provided weekday format is not supported.
     */
    private function formatWeekDay(string $format = 'w'): mixed
    {
        $supportedDayFormats = ['w', 'D', 'l'];

        if (!in_array($format, $supportedDayFormats)) {
            throw new Exception('Unsupported day format. Please use "w", "D", or "l".');
        }

        if (in_array($format, ['D', 'l'])) {
            return $this->defaultLang->getWeek($this->date['w']() - 1)[$format];
        }

        return $this->date[$format];
    }

    /**
     * Processes the given format character and returns the formatted value.
     *
     * @param string $formatChar The format character to process.
     * @return string
     * @throws Exception
     */
    private function processFormatChar(string $formatChar): string
    {
        if (in_array($formatChar, ['D', 'l'])) {
            return $this->formatWeekDay($formatChar);
        }

        if (in_array($formatChar, ['M', 'F'])) {
            return $this->formatMonth($formatChar);
        }

        if ($formatChar == 'g') {
            return $this->defaultLang->getGate();
        }

        return $this->formatNumber($formatChar == 'w' ? $this->date[$formatChar]() : $this->date[$formatChar], $this->defaultLang);
    }
}
