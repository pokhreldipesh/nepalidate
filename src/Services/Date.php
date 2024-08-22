<?php

namespace Dipesh\NepaliDate\Services;

use Dipesh\NepaliDate\Concerns\HasDateOperation;
use Dipesh\NepaliDate\Contracts\DateProcessor;
use Dipesh\NepaliDate\Contracts\Formatter;
use Dipesh\NepaliDate\Contracts\Language;
use Dipesh\NepaliDate\lang\English;
use Dipesh\NepaliDate\lang\Nepali;
use Dipesh\NepaliDate\Services\DateProcessor as serviceDateProcessor;
use Exception;

/**
 * Class Date
 *
 * @property int $weekDay Represents a Nepali Date object, providing methods to manipulate and retrieve date components.
 */
class Date implements \Dipesh\NepaliDate\Contracts\Date
{
    use HasDateOperation;

    /**
     * @var string The formatted date string (e.g., "2078/01/01").
     */
    public string $date;

    /**
     * @var int The day component of the date.
     */
    public int $day;

    /**
     * @var int The month component of the date.
     */
    public int $month;

    /**
     * @var int The year component of the date.
     */
    public int $year;

    /**
     * @var Language The language used for formatting date components.
     */
    public Language $language;

    /**
     * @var DateProcessor The daysCalculator used for calculate days from BS table
     */
    public DateProcessor $dateProcessor;

    /**
     * @var Formatter The formatter used for formatting date
     */
    public Formatter $formatter;

    public static $defaultOutputFormat = '%04d/%02d/%02d';

    /**
     * Constructor for initializing the date object with a specific date and language.
     *
     * @param  string  $date  The date to initialize the date object.
     * @param  Language  $language  An instance of the Language class used to configure language-specific settings.
     *
     * @throws Exception If the date setup fails or an invalid date is provided.
     */
    public function __construct(string $date, Language $language)
    {
        // Set the language configuration based on the provided Language instance.
        $this->language = $this->resolveLanguage($language);

        // Initialize the date processor, which will be used for date-related calculations throughout the object.
        $this->dateProcessor = $this->getDateProcessor();

        // Initialize the formatter, which will handle the formatting of dates based on the language and date settings.
        $this->formatter = $this->getFormatter();

        // Set up the date object with the provided date. This setup might be called from other parts of the code if needed.
        $this->setUp($date);
    }

    /**
     * Sets up the date object by parsing and validating the provided date string.
     *
     * @param  string  $date  The date string to set up (e.g., "2078/01/01").
     *
     * @throws Exception If the date format is invalid.
     */
    public function setUp(string $date): void
    {
        [$this->year, $this->month, $this->day] = self::validateDateAndGetComponents($date);
        $this->date = sprintf(self::$defaultOutputFormat, $this->year, $this->month, $this->day);
        $this->formatter->setUp($this);
    }

    /**
     * Retrieve a formatter instance for formatting dates.
     *
     * This method returns a new instance of the Formatter class,
     * which provides various options for formatting dates according
     * to the Nepali calendar and language settings.
     *
     * @return Formatter An instance of the FormatDate class for date formatting.
     */
    public function getFormatter(): Formatter
    {
        return new FormatDate;
    }

    /**
     * Retrieve a date processor object for date calculation from the BS calendar.
     *
     * This method returns an instance of the DateProcessor class,
     * which contains logic specific to calculating days within the
     * Bikram Sambat (BS) calendar system.
     *
     * @return DateProcessor An instance of serviceDateProcessor for date calculations.
     */
    public function getDateProcessor(): DateProcessor
    {
        return new serviceDateProcessor;
    }

    /**
     * Magic method for lazy loading the `weekDay` property.
     *
     * This method is triggered when accessing the `weekDay` property on
     * the object. If the `weekDay` property is requested, it calculates
     * the weekday based on the total days from the base date. This
     * approach is used to optimize performance by deferring the calculation
     * until the property is actually needed.
     *
     * @param  string  $name  The name of the property being accessed.
     * @return int The value of the requested property.
     *
     * @throws Exception If the property does not exist or is not accessible.
     */
    public function __get(string $name): int
    {
        if ($name === 'weekDay') {
            return $this->dateProcessor->getWeekDayFromDays($this->getTotalDaysFromBaseDate($this->date));
        }
        throw new Exception("Undefined property {$name}");
    }

    /**
     * Validates the date string and returns its components as an array.
     *
     * @param  string  $date  The date string to validate.
     * @return array An array containing year, month, and day.
     *
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
        return $this->formatter->formatNumber($this->day);
    }

    /**
     * Retrieves the month component, formatted according to the language.
     *
     * @return int|string The month component, formatted in the specified language.
     *
     * @throws Exception
     */
    public function month(string $format = 'm'): int|string
    {
        return $this->formatter->formatMonth($format);
    }

    /**
     * Retrieves the year component, formatted according to the language.
     *
     * @return int|string The year component, formatted in the specified language.
     */
    public function year(): int|string
    {
        return $this->formatter->formatNumber($this->year);
    }

    /**
     * Resolve Language Instance
     *
     * Resolves the provided language code or instance to a Language object.
     *
     * @param  string|Language  $language  The language code ('np' for Nepali, 'en' for English) or Language instance.
     *
     * @throws Exception If an unsupported language type is provided.
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
     * Format Nepali Date
     *
     * Formats the Nepali date according to the specified format string and language.
     *
     * @param  string  $format  The format string.
     * @param  string|Language|null  $lang  The language code or Language instance. Defaults to the current language.
     *
     * @throws Exception
     */
    public function format(string $format = 'Y/m/d', string|Language|null $lang = null): string
    {
        if ($lang) {
            $this->language = $this->resolveLanguage($lang);
        }

        return $this->formatter->setUp($this)->format($format);
    }
}
