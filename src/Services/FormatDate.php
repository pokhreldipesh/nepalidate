<?php

namespace Dipesh\NepaliDate\Services;


use Dipesh\NepaliDate\Contracts\Date;
use Dipesh\NepaliDate\Contracts\Formatter;
use Dipesh\NepaliDate\Contracts\Language;
use Dipesh\NepaliDate\lang\English;
use Exception;

class FormatDate implements  Formatter
{
    private array $date = ['Y', 'm', 'd', 'w'];

    private $calender;

    protected array $supportedFormats = ['Y', 'm', 'M', 'F', 'd', 'w', 'D', 'l', 'g'];

    protected Language $defaultLang;

    /**
     * @param Date|null $calender
     */
    public function __construct(Date $calender = null, Language|string $lang = new English())
    {
        if($calender) {
            $this->setUp(calender:$calender, lang:$lang);
        }
    }

    /**
     * Setup formatter
     *
     * @param Date $calender
     * @param Language $lang
     * @return FormatDate
     */
    public function setUp(Date $calender, Language $lang): static
    {
        $this->calender = $calender;
        $this->defaultLang = $lang;

        $this->date = array_combine($this->date, [$calender->year, $calender->month, $calender->day, $calender->weekDay()]);

        return $this;
    }

    /**
     * Format given date
     *
     * @param string $format
     *
     * @return string
     * @throws Exception
     */
    public function format(string $format = 'Y/m/d'): string
    {
        //dd($format);
        $this->checkForSupportedDateFormats($format);

        return preg_replace_callback("/\w*/m", function ($matches)  {
            if ($format = $matches[0]) {
                if (!in_array($format, ['Y', 'm', 'd', 'w'])) {
                    // check for supported format
                    if (in_array($format, ['D', 'l'])) {
                        return $this->formatWeekDay($format);
                    } elseif (in_array($format, ['M', 'F'])) {
                        return $this->formatMonth($format);
                    } elseif ($format == 'g') {
                        return $this->defaultLang->getGate();
                    }
                }

                return self::formatNumberToLanguage($this->date[$format], $this->defaultLang);

            }
            return null;

        }, $format);
    }

    /**
     * Convert number to nepali number
     *
     * @param int $number
     * @param Language $language
     * @return string
     */
    public static function formatNumberToLanguage(int $number, Language $language): string
    {
        return preg_replace_callback("/\d/m", function ($matches) use ($language) {
            return $language->getDigit($matches[0]);
        }, $number);
    }

    /**
     * validate supported date format
     *
     * @throw ?Exception
     * @throws Exception
     */
    private function checkForSupportedDateFormats($format): void
    {
        preg_match_all('/\w*/m', $format, $matches);

        if (!(count(array_filter($matches[0])) && (count(array_intersect(array_filter($matches[0]), $this->supportedFormats)) == count(array_filter($matches[0]))))) {
            throw new Exception('Invalid date format');
        }
    }

    /**
     * @param string $format
     * @return int|mixed|string
     * @throws Exception
     */
    public function formatMonth(string $format = 'm'): mixed
    {
        $supportedMonthFormat = ['m', 'M', 'F'];

        if (!in_array($format, $supportedMonthFormat)) {
            throw new Exception('Provided month format is not supported. please provide  m,M,F format');
        }

        if (in_array($format, ['M', 'F'])) {
            return $this->defaultLang->getMonth($this->calender->month - 1);
        }

        return $this->date[$format];
    }

    /**
     * @param string $format
     * @return mixed|string
     * @throws Exception
     */
    private function formatWeekDay(string $format = 'w'): mixed
    {
        $supportedDayFormat = ['w', 'D', 'l'];

        if (!in_array($format, $supportedDayFormat)) {
            throw new Exception('Provided day format is not supported. please provide  d,D,l format');
        }

        if (in_array($format, ['D', 'l'])) {

            return $this->defaultLang->getWeek($this->calender->weekDay() - 1 )[$format];
        }

        return $this->date[$format];
    }

}
