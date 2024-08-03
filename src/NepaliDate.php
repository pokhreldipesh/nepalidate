<?php

namespace Dipesh\NepaliDate;

use Carbon\Carbon;
use Dipesh\NepaliDate\Concerns\HasDate;
use Dipesh\NepaliDate\Contracts\Date;
use Dipesh\NepaliDate\Contracts\Formatter;
use Dipesh\NepaliDate\Contracts\Language;
use Dipesh\NepaliDate\lang\English;
use Dipesh\NepaliDate\lang\Nepali;
use Dipesh\NepaliDate\Services\DateOperation;
use Dipesh\NepaliDate\Services\FormatDate;
use Exception;


/**
 * Nepali Date
 *
 * @method self addDays(int $day)
 * @method int weekDay()
 * @method int getTotalDaysFromBaseDate()
 * @method int diffDays(string $date)
 * @method bool isEqual(string|Date $date)
 * @method bool isGreaterThan(string|Date $date)
 * @method bool isLessThan(string|Date $date)
 */
class NepaliDate implements Date
{
    use HasDate;

    public Language $formattingLanguage;

    /**
     * @throws Exception
     * @property string $date
     */
    public function __construct(string $date)
    {
        $this->setUp($date);
    }

    /**
     * @param string $date
     * @return self
     * @throws Exception
     */
    public static function make(string $date): self
    {
        return  new static($date);
    }

    /**
     * Set default formatting language
     *
     * @param string|Language $language
     * @return $this
     * @throws Exception
     */
    public function setLang(string|Language $language): static
    {
        $instance = clone $this;
        $instance->formattingLanguage = $this->resolveLanguage($language);

        return $instance;
    }

    /**
     * Resolve language
     *
     * @param string|Language $language
     * @return Language
     * @throws Exception
     */
    private function resolveLanguage(string|Language $language): Language
    {
        if ($language instanceof Language) {
            return $language;
        } elseif ($language == 'np') {
            return new Nepali();
        } elseif ($language == 'en') {
            return new English();
        } else {
            throw new Exception("Unsupported language type");
        }
    }

    /**
     * Format nepali date to provided language
     *
     * @param string $format
     * @param Formatter $formatter
     * @param string|Language|null $lang
     * @return string
     * @throws Exception
     */
    public function format(string $format, Formatter $formatter = new FormatDate(), string|Language $lang = null): string
    {
        return (new $formatter())->setUp(calender: $this, lang: $lang ? $this->resolveLanguage($lang) : $this->formattingLanguage)->format($format);
    }

    /**
     * Magic method to call calendar operations functions
     *
     * @param string $name
     * @param array|string $arguments
     *
     * @return int|self
     * @throws Exception
     */
    public function __call(string $name, array|string $arguments): int|self
    {
        if ($name == 'addDays')
        {
             $instance = clone $this;
             $instance->setUp($this->calenderOperation->addDays(...$arguments));
             return $instance;
        }

        return $this->calenderOperation->$name(...$arguments);
    }

    /**
     * Get current date
     *
     * @return $this
     * @throws Exception
     */
    public static function now(): static
    {
        return self::fromADDate(Carbon::now()->format("Y-m-d"));
    }
    /**
     * Convert calender date to AD
     *
     * @return Carbon
     * @throws Exception
     */
    public function toAd(): Carbon
    {
        return Carbon::parse(self::$baseEnglishDate)->addDays(
            $this->calenderOperation->getTotalDaysFromBaseDate($this->date)
        );
    }

    /**
     * Convert date from AD to BS
     *
     * @param string $date
     * @return static
     * @throws Exception
     */
    public static function fromADDate(string $date): static
    {
        return (new static(self::$equivalentNepaliDate))->addDays(Carbon::parse(self::$baseEnglishDate)->diffInDays($date));
    }

    /**
     * Get day from date
     *
     * @return int|string
     */
    public function day(): int|string
    {
        return FormatDate::formatNumberToLanguage($this->day, $this->formattingLanguage);
    }

    /**
     * Get month from date
     *
     * @return int|string
     */
    public function month(): int|string
    {
        return FormatDate::formatNumberToLanguage($this->month, $this->formattingLanguage);
    }

    /**
     * Get year from date
     *
     * @return int|string
     */
    public function year(): int|string
    {
        return FormatDate::formatNumberToLanguage($this->year, $this->formattingLanguage);
    }
    public function __clone(): void
    {
        $this->calenderOperation = new DateOperation($this);
    }

    /**
     * Calender date string
     *
     * @return string
     */
    public function __toString(): string
    {
        return $this->date;
    }
}
