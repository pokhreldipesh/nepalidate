<?php

namespace Dipesh\NepaliDate;

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
 * @method self subDays(int $day)
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

    public string $date;

    public int $day;

    public int $month;

    public int $year;

    protected DateOperation $calenderOperation;

    public Language $formattingLanguage;

    /**
     * @throws Exception
     * @property ?string $date
     */
    public function __construct(string $date = null, Language $language = new English())
    {
        $this->formattingLanguage = $language;

        $this->calenderOperation = new DateOperation($this);

        if ($date === null) {

            $date = self::now();
        }

        $this->setUp($date);
    }

    /**
     * Initialize calender
     *
     * @param string $date
     * @return void
     * @throws Exception
     */
    public function setUp(string $date): void
    {
        list(
            $this->year,
            $this->month,
            $this->day,
            ) = self::validateDateAndGetRaw($date);

        $this->date = implode("/", [$this->year, $this->month, $this->day]);
    }

    /**
     * Create date from instance
     *
     * @param string $date
     * @return NepaliDate|$this
     * @throws Exception
     */
    public function create(string $date): static
    {
        $instance = clone $this;
        $instance->setUp($date);

        return $instance;
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
            throw new Exception("The specified language type is not supported.");
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
     * Magic method to call date operations
     *
     * @param string $name
     * @param array|string $arguments
     *
     * @return bool|int|self
     * @throws Exception
     */
    public function __call(string $name, array|string $arguments): bool|int|self
    {
        if ($name == 'addDays' || $name == "subDays")
        {
             $instance = clone $this;
             $instance->setUp($this->calenderOperation->$name(...$arguments));
             return $instance;
        }

        return $this->calenderOperation->$name(...$arguments);
    }

    /**
     * @return void
     */
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
