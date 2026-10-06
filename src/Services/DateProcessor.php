<?php

namespace Dipesh\NepaliDate\Services;

use Dipesh\NepaliDate\Contracts\DateProcessor as DateProcessorContract;
use Dipesh\NepaliDate\DataSet;
use Dipesh\NepaliDate\InvalidDateRangeException;
use Dipesh\NepaliDate\SystemDataSet;
use Exception;

/**
 * Day-math engine over a BS calendar DataSet.
 *
 * Converts between (year, month, day) and cumulative day counts.
 * Falls back to the packaged SystemDataSet when no custom DataSet is provided.
 */
class DateProcessor implements DateProcessorContract
{
    /**
     * Base weekday of the calendar epoch (1 = Sunday … 7 = Saturday).
     */
    private const int BASE_WEEK_DAY = 7;

    /**
     * sprintf pattern for normalizing date strings.
     */
    private const string DATE_FORMAT = '%04d/%02d/%02d';

    private ?DataSet $defaultDataSet = null;

    public function __construct(private readonly ?DataSet $dataSet = null) {}

    public function getDataSet(): ?DataSet
    {
        return $this->dataSet;
    }

    /**
     * Resolve the active calendar — custom dataset or packaged system default.
     */
    private function calendar(): DataSet
    {
        return $this->dataSet ?? ($this->defaultDataSet ??= SystemDataSet::packaged());
    }

    /**
     * @return array<int, array<int, int>>
     */
    private function calendarRows(): array
    {
        return iterator_to_array($this->calendar());
    }

    /**
     * Total days from the start of the calendar table to the given date.
     *
     * @throws Exception If the year is outside the calendar range.
     */
    public function getDays(int $year, int $month, int $day): int
    {
        $bs = $this->calendarRows();

        if (! isset($bs[$year])) {
            throw new InvalidDateRangeException;
        }

        $totalDays = 0;

        foreach ($bs as $y => $months) {
            if ($y < $year) {
                $totalDays += array_sum($months);
            } elseif ($y === $year) {
                $totalDays += array_sum(array_slice($months, 0, $month - 1)) + $day;
                break;
            }
        }

        return $totalDays;
    }

    /**
     * Days from the dataset's equivalent Nepali base date (0 on that date).
     *
     * @internal Use getDays() arithmetic instead. Kept public for existing tests.
     */
    public function getDaysFromBase(int $year, int $month, int $day): int
    {
        [$baseYear, $baseMonth, $baseDay] = $this->parseYmd($this->calendar()->getEquivalentNepaliDate());

        return $this->getDays($year, $month, $day) - $this->getDays($baseYear, $baseMonth, $baseDay);
    }

    /**
     * Convert a cumulative day count back to a BS date string.
     *
     * @param  int  $totalDays  Days from the start of the calendar table.
     * @return string Date in "YYYY/MM/DD" format.
     *
     * @throws Exception If the day count exceeds the calendar range.
     */
    public function getDateFromDays(int $totalDays): string
    {
        $bs = $this->calendarRows();
        $accumulatedDays = 0;

        foreach ($bs as $year => $months) {
            $daysInYear = array_sum($months);

            if ($totalDays > $accumulatedDays + $daysInYear) {
                $accumulatedDays += $daysInYear;

                continue;
            }

            foreach ($months as $monthIndex => $monthDays) {
                if ($totalDays <= $accumulatedDays + $monthDays) {
                    return sprintf(
                        self::DATE_FORMAT,
                        $year,
                        $monthIndex + 1,
                        $totalDays - $accumulatedDays
                    );
                }
                $accumulatedDays += $monthDays;
            }
        }

        throw new InvalidDateRangeException;
    }

    /**
     * Map a cumulative day count to a weekday (1 = Sunday … 7 = Saturday).
     */
    public function getWeekDayFromDays(int $days): int
    {
        $day = $days % self::BASE_WEEK_DAY;

        if ($day < 0) {
            $day += self::BASE_WEEK_DAY;
        }

        return $day === 0 ? self::BASE_WEEK_DAY : $day;
    }

    /**
     * @return array{0: int, 1: int, 2: int}
     */
    private function parseYmd(string $date): array
    {
        [$year, $month, $day] = array_map(intval(...), explode('/', $date));

        return [$year, $month, $day];
    }
}
