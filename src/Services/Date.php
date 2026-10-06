<?php

namespace Dipesh\NepaliDate\Services;

use Dipesh\NepaliDate\Contracts\Formatter;
use Dipesh\NepaliDate\Contracts\Language;
use Dipesh\NepaliDate\lang\English;
use Dipesh\NepaliDate\lang\Nepali;
use Exception;

/**
 * System-level date value object.
 *
 * Parses and holds a BS (Bikram Sambat) date string and its components.
 * Delegates formatting to a Formatter. Contains no calendar arithmetic,
 * no DataSet, and no DateProcessor — those live on NepaliDate.
 */
/**
 * @phpstan-consistent-constructor
 */
class Date implements \Dipesh\NepaliDate\Contracts\Date
{
    /**
     * @var string Normalized date string (e.g. "2078/01/01").
     */
    public string $date;

    /**
     * @var int Year component.
     */
    public int $year;

    /**
     * @var int Month component (1-12).
     */
    public int $month;

    /**
     * @var int Day component.
     */
    public int $day;

    /**
     * @var int Day of the week (1 = Sunday … 7 = Saturday).
     */
    public int $weekDay = 0;

    /**
     * @var Language Language used for formatting.
     */
    public Language $language;

    /**
     * @var Formatter Formatter instance.
     */
    public Formatter $formatter;

    /**
     * @var string sprintf pattern used to normalize the date string.
     */
    public static string $defaultOutputFormat = '%04d/%02d/%02d';

    /**
     * @param  string  $date  Date string in "YYYY/MM/DD" format (separators may vary).
     * @param  Language  $language  Language instance or code.
     *
     * @throws Exception If the date format is invalid.
     */
    public function __construct(string $date, Language $language)
    {
        $this->language = $this->resolveLanguage($language);
        $this->assignDate($date);
    }

    /**
     * Ensure clones get a fresh formatter bound to the clone, not the original.
     */
    public function __clone(): void
    {
        $this->formatter = $this->getFormatter();
    }

    /**
     * Create a new instance with a different date, preserving language.
     *
     * @throws Exception If the date format is invalid.
     */
    public function withDate(string $date): static
    {
        return new static($date, $this->language);
    }

    /**
     * Parse and assign date components from a date string.
     *
     * Also creates a fresh formatter bound to this instance.
     * Called by the constructor. Subclasses may call this from
     * their own mutation methods (e.g. NepaliDate::setUp).
     *
     * @param  string  $date  Date string to parse.
     *
     * @throws Exception If the date format is invalid.
     */
    protected function assignDate(string $date): void
    {
        [$this->year, $this->month, $this->day] = self::parseComponents($date);
        $this->date = sprintf(self::$defaultOutputFormat, $this->year, $this->month, $this->day);
        $this->formatter = $this->getFormatter();
    }

    /**
     * Create a Formatter bound to this date instance.
     */
    public function getFormatter(): Formatter
    {
        return new FormatDate($this);
    }

    /**
     * Parse a date string into its year, month, and day components.
     *
     * Accepts flexible separators (e.g. "2078/01/01", "2078-01-01", "2078.1.1").
     * Validates structural constraints only (year >= 1, month 1-12, day >= 1).
     * Does not validate against a calendar — BS months can have up to 32 days.
     *
     * @return array{0: int, 1: int, 2: int} Year, month, day.
     *
     * @throws Exception If the date string does not match the expected shape.
     */
    public static function parseComponents(string $date): array
    {
        if (! preg_match('/^\s*(\d{1,4})\D+(\d{1,2})\D+(\d{1,2})\s*$/', $date, $matches)) {
            throw new Exception("Invalid date format. Please use 'YYYY/MM/DD'.");
        }

        [$year, $month, $day] = array_map('intval', [$matches[1], $matches[2], $matches[3]]);

        if ($year < 1 || $month < 1 || $month > 12 || $day < 1) {
            throw new Exception("Invalid date format. Please use 'YYYY/MM/DD'.");
        }

        return [$year, $month, $day];
    }

    /**
     * Get the normalized date string.
     */
    public function getDate(): string
    {
        return $this->date;
    }

    /**
     * Get the year as a raw integer.
     */
    public function getYear(): int
    {
        return $this->year;
    }

    /**
     * Get the month as a raw integer (1–12).
     */
    public function getMonth(): int
    {
        return $this->month;
    }

    /**
     * Get the day as a raw integer.
     */
    public function getDay(): int
    {
        return $this->day;
    }

    /**
     * Get the day of the week (1 = Sunday … 7 = Saturday).
     */
    public function getWeekDay(): int
    {
        return $this->weekDay;
    }

    /**
     * Get the current formatting language.
     */
    public function getLanguage(): Language
    {
        return $this->language;
    }

    /**
     * Get the day component, formatted for the current language.
     */
    public function day(): int|string
    {
        return $this->formatter->formatNumber($this->day);
    }

    /**
     * Get the month component, formatted for the current language.
     *
     * @param  string  $format  'm' for zero-padded number, 'M' for short name, 'F' for full name.
     *
     * @throws Exception If the format is unsupported.
     */
    public function month(string $format = 'm'): int|string
    {
        return $this->formatter->formatMonth($format);
    }

    /**
     * Get the year component, formatted for the current language.
     */
    public function year(): int|string
    {
        return $this->formatter->formatNumber($this->year);
    }

    /**
     * Resolve a language code or instance to a Language object.
     *
     * @param  string|Language  $language  'en', 'np', or a Language instance.
     *
     * @throws Exception If the language is not supported.
     */
    public function resolveLanguage(string|Language $language): Language
    {
        return match (true) {
            $language instanceof Language => $language,
            $language === 'np' => new Nepali,
            $language === 'en' => new English,
            default => throw new Exception('The specified language type is not supported.'),
        };
    }

    /**
     * Format the date according to a format string.
     *
     * Supported characters: Y, m, M, F, d, w, D, l, g.
     * The optional $lang override is temporary — the instance's
     * language is unchanged after the call.
     *
     * @param  string  $format  Format string.
     * @param  string|Language|null  $lang  Optional language override.
     *
     * @throws Exception If the format is invalid.
     */
    public function format(string $format = 'Y/m/d', string|Language|null $lang = null): string
    {
        if ($lang === null) {
            return $this->formatter->format($format);
        }

        $originalLanguage = $this->language;
        $this->language = $this->resolveLanguage($lang);
        $this->formatter = $this->getFormatter();

        try {
            return $this->formatter->format($format);
        } finally {
            $this->language = $originalLanguage;
            $this->formatter = $this->getFormatter();
        }
    }
}
