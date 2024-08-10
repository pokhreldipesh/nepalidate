<?php

namespace Dipesh\NepaliDate\Services;

use Dipesh\NepaliDate\Concerns\HasDateOperation;
use Dipesh\NepaliDate\Contracts\DaysCalculator;
use Dipesh\NepaliDate\Contracts\Formatter;
use Dipesh\NepaliDate\Contracts\Language;
use Dipesh\NepaliDate\lang\English;
use Dipesh\NepaliDate\lang\Nepali;
use Dipesh\NepaliDate\Services\DaysCalculator as ServicesDaysCalculator;
use Exception;

/**
 * Class Date
 *
 * Represents a Nepali Date object, providing methods to manipulate and retrieve date components.
 */
class Date implements \Dipesh\NepaliDate\Contracts\Date
{
    use HasDateOperation;
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
     * @var Language $language The language used for formatting date components.
     */
    public Language $language;

    /**
     * @var DaysCalculator $daysCalculator The daysCalculator used for calculate days from BS table
     */
    public DaysCalculator $daysCalculator;

    /**
     * @var Formatter $formatter The formatter used for formatting date
     */
    public Formatter $formatter;

    /**
     * @throws Exception
     */
    public function __construct(string $date, Language $language)
    {
        $this->language = $this->resolveLanguage($language);
        $this->daysCalculator = new ServicesDaysCalculator();
        $this->formatter = new FormatDate();
        $this->setUp($date);
    }

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
    private static function validateDateAndGetComponents(string $date): array
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
        return $this->formatter->formatNumber($this->day, $this->language);
    }

    /**
     * Retrieves the month component, formatted according to the language.
     *
     * @return int|string The month component, formatted in the specified language.
     */
    public function month(): int|string
    {
        return $this->formatter->formatNumber($this->month, $this->language);
    }

    /**
     * Retrieves the year component, formatted according to the language.
     *
     * @return int|string The year component, formatted in the specified language.
     */
    public function year(): int|string
    {
        return $this->formatter->formatNumber($this->year, $this->language);
    }

    /**
     * Resolve Language Instance
     *
     * Resolves the provided language code or instance to a Language object.
     *
     * @param string|Language $language  The language code ('np' for Nepali, 'en' for English) or Language instance.
     * @return Language
     * @throws Exception  If an unsupported language type is provided.
     */
    public function resolveLanguage(string|Language $language): Language
    {
        return match(true) {
            $language instanceof Language => $language,
            $language === 'np' => new Nepali(),
            $language === 'en' => new English(),
            default => throw new Exception("The specified language type is not supported."),
        };
    }

    /**
     * Format Nepali Date
     *
     * Formats the Nepali date according to the specified format string and language.
     *
     * @param string $format The format string.
     * @param string|Language|null $lang The language code or Language instance. Defaults to the current language.
     * @param Formatter|null $formatter The formatter instance used for formatting the date.
     * @return string
     * @throws Exception
     */
    public function format(string $format, string|Language $lang = null, Formatter $formatter = null): string
    {
        if ($formatter) {
            return $formatter($format, $this, $lang ? $this->resolveLanguage($lang) : $this->language);

        }
        return $this->formatter->__invoke($format, $this, $lang ? $this->resolveLanguage($lang) : $this->language);
    }
}
