<?php

namespace Dipesh\NepaliDate;

use Dipesh\NepaliDate\Concerns\HasDateComparison;
use Dipesh\NepaliDate\Concerns\HasDateConversion;
use Dipesh\NepaliDate\Concerns\HasDateManipulation;
use Dipesh\NepaliDate\Contracts\Language;
use Dipesh\NepaliDate\lang\English;
use Dipesh\NepaliDate\Services\Date;
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
    use HasDateComparison, HasDateConversion, HasDateManipulation;

    /**
     * NepaliDate Constructor
     *
     * Initializes the NepaliDate instance with a given date string and language.
     * If no date is provided, the current date is used. It sets up the necessary
     * language formatting and days calculator.
     *
     * @param  string|null  $date  The date string in Nepali date format. Defaults to current date if null.
     * @param  Language|null  $language  The language used for formatting. Defaults to English.
     *
     * @throws Exception
     */
    public function __construct(string $date = null, Language $language = null)
    {
        parent::__construct($date ?? self::now(), $language ?? new English);
    }

    /**
     * Get Current Date
     *
     * Returns an instance of NepaliDate set to the current date.
     *
     * @throws Exception
     */
    public static function now(): static
    {
        return self::fromADDate((new EnDate)->format('Y-m-d'));
    }

    /**
     * Create a New Instance with a Given Date
     *
     * Returns a new instance of NepaliDate initialized with the provided date.
     *
     * @param  string  $date  The date string in Nepali date format.
     *
     * @throws Exception
     */
    public function create(string $date): static
    {
        $newDateInstance = clone $this;
        $newDateInstance->setUp($date);

        return $newDateInstance;
    }

    /**
     * Create a New Instance
     *
     * Returns a new instance of NepaliDate with the provided date string.
     *
     * @param  string  $date  The date string in Nepali date format.
     *
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
     * @param  string|Language  $language  The language code or Language instance.
     *
     * @throws Exception
     */
    public function setLang(string|Language $language): static
    {
        $instance = clone $this;
        $instance->language = $this->resolveLanguage($language);
        $instance->formatter = clone $instance->formatter->setUp($instance);

        return $instance;
    }

    /**
     * Convert to String
     *
     * Returns the Nepali date as a string.
     */
    public function __toString(): string
    {
        return $this->date;
    }
}
