<?php

declare(strict_types=1);

namespace Dipesh\NepaliDate;

use Dipesh\Date\Contracts\Formatter;
use Dipesh\Date\Contracts\LanguagePack;
use Dipesh\Date\Date;
use Dipesh\Date\lang\English;
use Dipesh\Date\Language;
use Dipesh\NepaliDate\Concerns\HasDateConversion;
use Dipesh\NepaliDate\Concerns\HasDateManipulation;
use Dipesh\NepaliDate\Concerns\HasDateOperation;
use Dipesh\NepaliDate\Contracts\DateProcessor as DateProcessorContract;
use Dipesh\NepaliDate\Services\DateProcessor;
use Stringable;

/**
 * NepaliDate — calendar-aware BS date facade.
 *
 * Composes a system-level {@see \Dipesh\Date\Date} (from `dipesh/date`) and delegates
 * every date-level concern to it. This class only owns calendar awareness: the
 * `DataSet`, day-math, weekday resolution, and AD ↔ BS conversion.
 *
 * For date-related operations, use the composed object directly:
 *
 *     $nepali->date->format('Y F d g l');
 *     $nepali->date->getYear();
 *
 * The methods available on that `Date` are also forwarded from this facade (see
 * the `@method` list below), so `$nepali->format('Y/m/d')` works too.
 *
 * All date-level types below come from the `dipesh/date` package namespace
 * (`\Dipesh\Date\…`); this package defines none of them.
 *
 * @property-read int $year Year component.
 * @property-read int $month Month component (1–12).
 * @property-read int $day Day component.
 * @property-read int $weekDay Day of the week (1 = Sunday … 7 = Saturday).
 * @property-read \Dipesh\Date\Language $language Formatting language.
 * @property-read \Dipesh\Date\Contracts\Formatter $formatter Formatter instance.
 *
 * @method string format(string $format = 'Y/m/d', string|\Dipesh\Date\Contracts\LanguagePack|\Dipesh\Date\Language|null $lang = null) Format the date.
 * @method string getDate() Normalized date string (e.g. "2081/04/25").
 * @method int getYear() Year as raw integer.
 * @method int getMonth() Month as raw integer (1–12).
 * @method int getDay() Day as raw integer.
 * @method int getWeekDay() Day of the week (1 = Sunday … 7 = Saturday).
 * @method \Dipesh\Date\Language getLanguage() Current formatting language.
 * @method \Dipesh\Date\Contracts\Formatter getFormatter() Formatter bound to the composed date.
 * @method \Dipesh\Date\Language resolveLanguage(string|\Dipesh\Date\Contracts\LanguagePack|\Dipesh\Date\Language $language) Resolve a language code, pack, or bridge.
 * @method bool isEqual(\Dipesh\NepaliDate\NepaliDate|\Dipesh\Date\Date|string $date) Component-wise equality.
 * @method bool isGreaterThan(\Dipesh\NepaliDate\NepaliDate|\Dipesh\Date\Date|string $date) Component-wise ordering.
 * @method bool isLessThan(\Dipesh\NepaliDate\NepaliDate|\Dipesh\Date\Date|string $date) Component-wise ordering.
 * @method static array{0: int, 1: int, 2: int} parseComponents(string $date) Parse into [year, month, day].
 *
 * @phpstan-consistent-constructor
 */
class NepaliDate implements Stringable
{
    use HasDateConversion, HasDateManipulation, HasDateOperation;

    /**
     * Composed system-level date value object.
     */
    public private(set) Date $date;

    /**
     * Day-math engine over the calendar dataset.
     */
    public private(set) DateProcessorContract $dateProcessor;

    /**
     * @param  string|null  $date  Date string in Nepali format. Defaults to current date.
     * @param  \Dipesh\Date\Contracts\LanguagePack|\Dipesh\Date\Language|null  $language  Formatting language. Defaults to English.
     * @param  DataSet|null  $dataSet  Optional custom calendar dataset.
     *
     * @throws \Exception
     */
    public function __construct(?string $date = null, LanguagePack|Language|null $language = null, public ?DataSet $dataSet = null)
    {
        $this->dateProcessor = $this->getDateProcessor();

        $this->date = $this->withComputedWeekDay(
            new Date($date ?? self::now($this->dataSet)->getDate(), $language ?? new English)
        );
    }

    /**
     * Create a DateProcessor for the current dataset.
     */
    public function getDateProcessor(): DateProcessorContract
    {
        return new DateProcessor($this->dataSet);
    }

    /**
     * Create a new instance with a different date, preserving language, dataset,
     * and any custom formatter.
     *
     * @throws \Exception
     */
    public function withDate(string $date): static
    {
        $new = clone $this;
        $new->date = $this->withComputedWeekDay($this->date->withDate($date));

        return $new;
    }

    /**
     * Mutate this instance to a new date and refresh the weekday.
     *
     * @throws \Exception
     */
    public function setUp(string $date): void
    {
        $this->date = $this->withComputedWeekDay($this->date->withDate($date));
    }

    /**
     * Get the year component, formatted for the current language.
     */
    public function year(): int|string
    {
        return $this->date->getFormatter()->formatNumber($this->date->getYear());
    }

    /**
     * Get the month component, formatted for the current language.
     *
     * @param  string  $format  'm' for zero-padded number, 'M' for short name, 'F' for full name.
     *
     * @throws \Exception If the format is unsupported.
     */
    public function month(string $format = 'm'): int|string
    {
        return $this->date->getFormatter()->formatMonth($format);
    }

    /**
     * Get the day component, formatted for the current language.
     */
    public function day(): int|string
    {
        return $this->date->getFormatter()->formatNumber($this->date->getDay());
    }

    /**
     * Get Current Date
     *
     * @param  DataSet|null  $dataSet  Optional custom calendar dataset.
     *
     * @throws \Exception
     */
    public static function now(?DataSet $dataSet = null): static
    {
        return self::fromADDate((new EnDate)->format('Y-m-d'), $dataSet);
    }

    /**
     * Create a New Instance with a Given Date
     *
     * @param  string  $date  Date string in Nepali format.
     *
     * @throws \Exception
     */
    public function create(string $date): static
    {
        return $this->withDate($date);
    }

    /**
     * Create a New Instance
     *
     * @param  string  $date  Date string in Nepali format.
     * @param  DataSet|null  $dataSet  Optional custom calendar dataset.
     *
     * @throws \Exception
     */
    public static function make(string $date, ?DataSet $dataSet = null): self
    {
        return new static($date, null, $dataSet);
    }

    /**
     * Set Default Formatting Language
     *
     * @param  string|\Dipesh\Date\Contracts\LanguagePack|\Dipesh\Date\Language  $language  Language code, pack, or bridge.
     *
     * @throws \Exception If the language is not supported.
     */
    public function setLang(string|LanguagePack|Language $language): static
    {
        $new = clone $this;
        $new->date = $this->date->languageUsing(
            is_string($language) ? $this->date->resolveLanguage($language) : $language
        );

        return $new;
    }

    /**
     * Create a new instance that formats through a custom formatter.
     *
     * Returns `static` (not the composed `Date`) so calendar operations stay
     * available further along a fluent chain.
     *
     * @param  \Dipesh\Date\Contracts\Formatter|callable  $formatter  Formatter instance, or a callback
     *                                                                invoked as ($format, $date).
     */
    public function formatUsing(Formatter|callable $formatter): static
    {
        $new = clone $this;
        $new->date = $this->date->formatUsing($formatter);

        return $new;
    }

    /**
     * Create a new instance with a different formatting language.
     *
     * Returns `static` (not the composed `Date`) so calendar operations stay
     * available further along a fluent chain.
     *
     * @param  \Dipesh\Date\Contracts\LanguagePack|\Dipesh\Date\Language|callable  $language  Pack, bridge, or a callback
     *                                                                                        returning language data.
     *
     * @throws \Exception If the language data is malformed.
     */
    public function languageUsing(LanguagePack|Language|callable $language): static
    {
        $new = clone $this;
        $new->date = $this->date->languageUsing($language);

        return $new;
    }

    /**
     * Determine if the current date is the anniversary of the specified date.
     *
     * An anniversary shares the same month and day, regardless of year — a date is
     * therefore its own anniversary. Components are compared directly, so BS month
     * lengths do not matter. Delegates to the composed {@see \Dipesh\Date\Date}.
     *
     * The `null` → today default lives here rather than on `dipesh/date` because
     * that package has no clock; it resolves through {@see self::now()} in the
     * active calendar.
     *
     * @param  \Dipesh\NepaliDate\NepaliDate|\Dipesh\Date\Date|string|null  $date  Reference date. Defaults to today.
     * @return bool True if both dates share the same month and day.
     *
     * @throws \Exception If the date string is invalid.
     */
    public function isAnniversary(NepaliDate|Date|string|null $date = null): bool
    {
        $reference = $date ?? self::now($this->dataSet);

        return $this->date->isAnniversary(
            $reference instanceof self ? $reference->date : $reference
        );
    }

    /**
     * Determine if the current date is the birthday of the specified date.
     *
     * A convenience alias for {@see isAnniversary()} — a birthday is simply the
     * anniversary of a birth date (same month and day, regardless of year).
     * Not provided by `dipesh/date`.
     *
     * @param  \Dipesh\NepaliDate\NepaliDate|\Dipesh\Date\Date|string|null  $date  Reference date. Defaults to today.
     * @return bool True if both dates share the same month and day.
     *
     * @throws \Exception If the date string is invalid.
     */
    public function isBirthday(NepaliDate|Date|string|null $date = null): bool
    {
        return $this->isAnniversary($date);
    }

    /**
     * Convert to String
     */
    public function __toString(): string
    {
        return $this->date->getDate();
    }

    /**
     * Read a date property from the composed {@see \Dipesh\Date\Date}.
     */
    public function __get(string $name): mixed
    {
        return $this->date->$name;
    }

    /**
     * Forward a date method call to the composed {@see \Dipesh\Date\Date}.
     *
     * Arguments that are `NepaliDate` instances are unwrapped to their composed
     * `Date`, so comparison helpers accept either.
     *
     * @param  array<int, mixed>  $arguments
     */
    public function __call(string $name, array $arguments): mixed
    {
        return $this->date->$name(...array_map($this->unwrap(...), $arguments));
    }

    /**
     * Forward a static date call (e.g. `parseComponents`) to {@see \Dipesh\Date\Date}.
     *
     * @param  array<int, mixed>  $arguments
     */
    public static function __callStatic(string $name, array $arguments): mixed
    {
        return Date::$name(...$arguments);
    }

    /**
     * Replace the composed Date with one carrying the weekday from this calendar.
     */
    private function withComputedWeekDay(Date $system): Date
    {
        return $system->withWeekDay(
            $this->dateProcessor->getWeekDayFromDays($this->getTotalDaysFromBaseDate($system))
        );
    }

    /**
     * Reduce a date reference to the composed Date or a date string.
     */
    private function unwrap(mixed $value): mixed
    {
        return $value instanceof self ? $value->date : $value;
    }
}
