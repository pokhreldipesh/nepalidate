<?php

namespace Dipesh\NepaliDate;

use Carbon\Carbon;
use Dipesh\NepaliDate\Concerns\HasDateConversion;
use Dipesh\NepaliDate\Concerns\HasDateComparison;
use Dipesh\NepaliDate\Concerns\HasDateManipulation;
use Dipesh\NepaliDate\Concerns\HasDateOperation;
use Dipesh\NepaliDate\Contracts\Formatter;
use Dipesh\NepaliDate\Contracts\Language;
use Dipesh\NepaliDate\Contracts\DaysCalculator;
use Dipesh\NepaliDate\lang\English;
use Dipesh\NepaliDate\lang\Nepali;
use Dipesh\NepaliDate\Services\Date;
use Dipesh\NepaliDate\Services\FormatDate;
use Dipesh\NepaliDate\Services\DaysCalculator as ServicesDaysCalculator;
use Exception;

/**
 * NepaliDate Class
 *
 * This class provides functionalities for handling Nepali dates, including
 * date conversion, manipulation, comparison, and formatting. It extends the
 * base Date class and utilizes multiple traits to offer comprehensive date
 * operations tailored for the Nepali calendar system.
 */
class NepaliDate extends Date
{
    use HasDateConversion, HasDateManipulation, HasDateComparison, HasDateOperation;

    /**
     * @var DaysCalculator
     */
    public DaysCalculator $daysCalculator;

    /**
     * NepaliDate Constructor
     *
     * Initializes the NepaliDate instance with a given date string and language.
     * If no date is provided, the current date is used. It sets up the necessary
     * language formatting and days calculator.
     *
     * @param string|null $date The date string in Nepali date format. Defaults to current date if null.
     * @param Language $language The language used for formatting. Defaults to English.
     * @throws Exception
     */
    public function __construct(string $date = null, Language $language = new English())
    {
        $this->formattingLanguage = $language;
        $this->daysCalculator = new ServicesDaysCalculator();

        $date = $date ?? self::now();
        $this->setUp($date);
    }

    /**
     * Get Current Date
     *
     * Returns an instance of NepaliDate set to the current date.
     *
     * @return static
     * @throws Exception
     */
    public static function now(): static
    {
        return self::fromADDate(Carbon::now()->format("Y-m-d"));
    }

    /**
     * Create a New Instance with a Given Date
     *
     * Returns a new instance of NepaliDate initialized with the provided date.
     *
     * @param string $date  The date string in Nepali date format.
     * @return static
     * @throws Exception
     */
    public function create(string $date): static
    {
        $instance = clone $this;
        $instance->setUp($date);

        return $instance;
    }

    /**
     * Create a New Instance
     *
     * Returns a new instance of NepaliDate with the provided date string.
     *
     * @param string $date  The date string in Nepali date format.
     * @return self
     * @throws Exception
     */
    public static function make(string $date): self
    {
        return new static($date);
    }

    /**
     * Set Default Formatting Language
     *
     * Sets the default language for date formatting. Returns a new instance
     * of NepaliDate with the specified language.
     *
     * @param string|Language $language  The language code or Language instance.
     * @return static
     * @throws Exception
     */
    public function setLang(string|Language $language): static
    {
        $instance = clone $this;
        $instance->formattingLanguage = $this->resolveLanguage($language);

        return $instance;
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
    private function resolveLanguage(string|Language $language): Language
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
     * @param string $format  The format string.
     * @param Formatter $formatter  The formatter instance used for formatting the date.
     * @param string|Language|null $lang  The language code or Language instance. Defaults to the current language.
     * @return string
     * @throws Exception
     */
    public function format(string $format, Formatter $formatter = new FormatDate(), string|Language $lang = null): string
    {
        return $formatter->setUp(calendar: $this, lang: $lang ? $this->resolveLanguage($lang) : $this->formattingLanguage)->format($format);
    }

    /**
     * Convert to String
     *
     * Returns the Nepali date as a string.
     *
     * @return string
     */
    public function __toString(): string
    {
        return $this->date;
    }
}
