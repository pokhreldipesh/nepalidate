<?php

namespace Dipesh\NepaliDate;

use Dipesh\NepaliDate\Core\Comparison;
use Dipesh\NepaliDate\Core\Date;
use Dipesh\NepaliDate\Core\NepaliDateConverter;
use Dipesh\NepaliDate\Core\Translator;

class NepaliDate implements Date
{
    use Comparison;

    /**
     * Converter instance.
     *
     * @var mixed
     */
    protected $converter;

    /**
     * Current converted date.
     *
     * @var array
     */
    protected $currentDate = [];

    /**
     * Current converted date stored in string format.
     *
     * @var mixed
     */
    protected $date = null;

    /**
     * From date in string.
     *
     * @var string
     */
    protected $fromDate = null;

    /**
     * Set default lang to en.
     *
     * @var mixed
     */
    protected $defaultLang = 'en';

    /**
     * Translator.for locale.
     */
    protected array $translator;

    /**
     * Set current date type.
     *
     * @var mixed
     */
    protected $currentDateType = null;

    /**
     * Supported date formats.
     *
     * @var mixed
     */
    protected $supportedFormats = ['Y', 'm', 'M', 'F', 'd', 'w', 'D', 'l', 'g'];

    /**
     * Initialize user date to be convert into eng or nepali.
     *
     * @param mixed $date
     */
    public function __construct($date = null)
    {
        $this->translator = (new Translator())($this->defaultLang);

        if ($date) {
            $this->setDate($date);
        }

        $this->converter = new NepaliDateConverter();
    }

    public function getMonths()
    {
        return $this->translator['months'];
    }

    public function getWeekDays()
    {
        return $this->translator['weeks'];
    }

    /**
     * Set date for further operation.
     *
     * @param mixed $date
     *
     * @return void
     */
    private function setDate($date)
    {
        $this->fromDate = $this->formatUserDate($date);
    }

    /**
     * Validate and format user date.
     *
     * @param mixed $date
     *
     * @return string
     *
     * @throws \Exception
     */
    private function formatUserDate($date)
    {
        preg_match_all("/\d*/m", $date, $matches);

        if (count(array_filter($matches[0])) < 3) {
            throw new \Exception('Invalid date format.');
        }

        return implode('/', array_filter($matches[0]));
    }

    /**
     * Push data to lookup table.
     *
     * ['2091' => [30, 32, 31, 32, 31, 30, 30, 30, 29, 30, 30, 30]].
     *
     * @param mixed $months
     */
    public function pushLookUpTable(array $data)
    {
        $this->converter->bs = $this->converter->bs + $data;

        return $this;
    }

    /**
     * Get lookup table for reference.
     *
     * @return array
     */
    public function getLookUpTable()
    {
        return $this->converter->bs;
    }

    /**
     * Set current date.
     *
     * @param mixed $year
     * @param mixed $month
     * @param mixed $day
     * @param mixed $week
     *
     * @return void
     */
    public function setCurrentDate($year, $month, $day, $week)
    {
        $this->currentDate = [
            'Y' => $year,
            'm' => $month,
            'd' => $day,
            'w' => $week,
        ];

        $this->date = $year.'/'.$month.'/'.$day;
    }

    /**
     * Convert given date to nepali.
     *
     * @return NepaliDate
     */
    public function toNepali($date = null)
    {
        if ($date) {
            $this->setDate($date);
        }

        $this->dateValidationBeforeConvert();

        $this->currentDateType = 'np';

        $this->setCurrentDate(...$this->converter->convert($this->fromDate));

        return $this;
    }

    /**
     * Convert nepali date to to english date.
     *
     * @param mixed $format
     *
     * @return string
     */
    public function toEnglish($date = null, $format = 'Y/m/d')
    {
        if ($date) {
            $this->setDate($date);
        }

        $this->dateValidationBeforeConvert();

        $this->currentDateType = 'en';
        $orgDate = $this->fromDate;

        // get current nepali date
        $this->fromDate = date('Y/m/d');
        $this->toNepali();

        $diffDays = $this->diff($orgDate);

        return date_create()->sub(date_interval_create_from_date_string("$diffDays days"))->format($format);
    }

    /**
     * Get week days.
     *
     * @param mixed $format
     * @param mixed $lang
     *
     * @return mixed
     *
     * @throws \Exception
     */
    public function weekDay($format = 'w', $lang = 'np')
    {
        $supportedDayFormat = ['w', 'D', 'l'];

        if (!in_array($format, $supportedDayFormat)) {
            throw new \Exception('Provided day format is not supported. please provide  d,D,l format');
        }

        if (in_array($format, ['D', 'l'])) {
            return $this->translator['weeks'][$this->currentDate['w'] - 1][$format];
        }

        return $this->currentDate[$format];
    }

    /**
     * Get day of current month.
     *
     * @param mixed $format
     *
     * @return int|mixed
     *
     * @throws \Exception
     */
    public function day($format = 'd')
    {
        $supportedDayFormat = ['d'];

        if (!in_array($format, $supportedDayFormat)) {
            throw new \Exception('Provided day format is not supported. please provide  d');
        }

        return $this->currentDate[$format];
    }

    /**
     * Get month of current year.
     *
     * @param mixed $format
     * @param mixed $lang
     *
     * @return mixed
     *
     * @throws \Exception
     */
    public function month($format = 'm', $lang = 'np')
    {
        $supportedMonthFormat = ['m', 'M', 'F'];

        if (!in_array($format, $supportedMonthFormat)) {
            throw new \Exception('Provided month format is not supported. please provide  m,M,F format');
        }

        if (in_array($format, ['M', 'F'])) {
            return $this->translator['months'][$this->currentDate['m'] - 1];
        }

        return $this->currentDate[$format];
    }

    /**
     * Get year of current year.
     *
     * @param mixed $format
     *
     * @return int|mixed
     */
    public function year($format = 'Y')
    {
        return $this->currentDate[$format];
    }

    /**
     * Add days to current date.
     *
     * @param mixed $days
     * @param mixed $date
     *
     * @return void
     */
    public function addDays($days, $date = null)
    {
        $this->fromDate = date_create($date ?? $this->fromDate)->add(date_interval_create_from_date_string("$days days"))->format('Y/m/d');

        $this->toNepali();
    }

    /**
     * Subtract date from current date.
     *
     * @param mixed $days
     *
     * @return void
     */
    public function subDays($days)
    {
        $this->fromDate = date_create($this->fromDate)->sub(date_interval_create_from_date_string("$days days"))->format('Y/m/d');

        $this->toNepali();
    }

    /**
     * Sub months from current date.
     *
     * @param mixed $months
     *
     * @return NepaliDate
     */
    public function subMonths($months)
    {
        $currentYear = $this->converter->bs[$this->currentDate['Y']];

        $monthIndex = $this->currentDate['m'] - $months - 1;

        if ($monthIndex < 0) {
            $y = abs(floor($monthIndex / 12)) + 1;

            $fromIndex = 12 - abs($monthIndex % 12);

            $currentYear = array_slice(array_merge(...array_slice($this->converter->bs, ($this->currentDate['Y'] - 2000) - $y + 1, $y)), $fromIndex);

            $subDays = array_sum(array_slice($currentYear, 0, $months));
        } else {
            $subDays = array_sum(array_slice($currentYear, $this->currentDate['m'] - $months - 1, $months));
        }

        $this->subDays($subDays);

        return $this;
    }

    /**
     * Add months to current date.
     *
     * @param mixed $months
     *
     * @return void
     */
    public function addMonths($months)
    {
        $currentYear = $this->converter->bs[$this->currentDate['Y']];

        if (($this->currentDate['m'] + $months) / 12 > 1) {
            $currentYear = array_merge(...array_slice($this->converter->bs, $this->currentDate['Y'] - 2000, ceil(($this->currentDate['m'] + $months) / 12)));
        }
        $days = array_sum(array_slice($currentYear, $this->currentDate['m'], $months));

        $this->addDays($days);
    }

    /**
     * Sub years from current date.
     *
     * @param mixed $years
     *
     * @return void
     */
    public function subYears($years)
    {
        $days = array_sum(array_merge(...array_slice($this->converter->bs, $this->currentDate['Y'] - 2000 - $years, $years)));

        $this->subDays($days);
    }

    /**
     * Get last day of nepali date.
     *
     * @return string
     */
    public function lastDayOfMonth()
    {
        return $this->formatUserDate($this->currentDate['Y'].'/'.$this->currentDate['m'].'/'.$this->converter->bs[$this->currentDate['Y']][$this->currentDate['m'] - 1]);
    }

    /**
     * Get first day of nepali date.
     *
     * @return string
     */
    public function firstDayOfMonth()
    {
        return $this->formatUserDate($this->currentDate['Y'].'/'.$this->currentDate['m'].'/1');
    }

    /**
     * Diff date from current date.
     *
     * @param mixed $date
     *
     * @return float
     */
    public function diff($date)
    {
        preg_match_all("/\d*/m", $date, $matches);

        return $this->converter->getDiffDaysFromNepaliDate(implode('/', array_filter($matches[0])), $this->date);
    }

    /**
     * Format nepali date.
     * Y = 4 degit year
     * m = A numeric representation of a month
     * M = A short textual representation of a month
     * F = A full textual representation of a month
     * d = The day of the month
     * w = A numeric representation of the day
     * D = A short textual representation of a day
     * l = A full textual representation of a day.
     *
     * @param mixed $format
     *
     * @return array|string|null
     */
    public function format($format = 'Y/m/d')
    {
        $this->checkForSupportedDateFormats($format);

        return preg_replace_callback("/\w*/m", function ($matches) {
            if ($format = $matches[0]) {
                if (!in_array($format, ['Y', 'm', 'd', 'w'])) {
                    // check for supported format
                    if (in_array($format, ['D', 'l'])) {
                        return $this->weekDay($format);
                    } elseif (in_array($format, ['M', 'F'])) {
                        return $this->month($format);
                    } elseif ($format == 'g') {
                        return $this->translator['gate'];
                    }
                }

                if ($this->defaultLang == 'np') {
                    return $this->convertNumberToNepali($this->currentDate[$format]);
                }

                return $this->currentDate[$format];
            }
        }, $format);
    }

    /**
     * Date validation before conversion.
     *
     * @return void
     *
     * @throws \Exception
     */
    private function dateValidationBeforeConvert()
    {
        if (empty($this->fromDate)) {
            throw new \Exception('Please provide valid date for conversion');
        }
    }

    /**
     * Validates date format.
     *
     * @param mixed $format
     *
     * @return void
     *
     * @throws \Exception
     */
    private function checkForSupportedDateFormats($format)
    {
        preg_match_all('/\w*/m', $format, $matches);

        if (!(count(array_filter($matches[0])) && (count(array_intersect(array_filter($matches[0]), $this->supportedFormats)) == count(array_filter($matches[0]))))) {
            throw new \Exception('Invalid date format');
        }
    }

    /**
     * Convert english number to nepali.
     *
     * @param mixed $number
     *
     * @return array|string|null
     */
    private function convertNumberToNepali($number)
    {
        return preg_replace_callback("/\d/m", function ($matches) {
            return $this->translator['digits'][$matches[0]];
        }, $number);
    }

    /**
     * Set default language.
     */
    public function lang($lang)
    {
        if (is_callable($lang)) {
            $this->translator = $lang($this->translator);
        } else {
            $this->translator = (new Translator())($lang);
            $this->defaultLang = $lang;
        }

        return $this;
    }

    /**
     * Return current date when objects echo.
     *
     * @return string|null
     */
    public function __toString()
    {
        return $this->date;
    }
}
