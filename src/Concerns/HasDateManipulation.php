<?php

namespace Dipesh\NepaliDate\Concerns;

use Exception;

trait HasDateManipulation
{
    /**
     * Add a specified number of days to the current date instance.
     *
     * This method calculates the total number of days by adding the provided days to the current date's
     * total days since the base date. It then creates a cloned instance of the current date object with
     * the updated date.
     *
     * @param int $day The number of days to add to the current date.
     * @return static Returns a new instance of the calling class with the updated date.
     * @throws Exception If an error occurs during the date calculation or setup.
     */
    public function addDays(int $day): static
    {
        $totalDays = $this->daysCalculator->totalDays($this->year, $this->month, $this->day) + $day;

        $cloned = clone $this;
        $cloned->setUp($this->daysCalculator->addDays($totalDays));

        return $cloned;
    }

    /**
     * Subtract a specified number of days from the current date instance.
     *
     * This method subtracts the provided number of days by passing the negative value to the `addDays` method.
     * It effectively reduces the date by the given number of days and returns a new instance with the updated date.
     *
     * @param int $day The number of days to subtract from the current date.
     * @return static Returns a new instance of the calling class with the updated date.
     * @throws Exception If an error occurs during the date calculation or setup.
     */
    public function subDays(int $day): static
    {
        return $this->addDays(-$day);
    }
}
