<?php

declare(strict_types=1);

namespace Dipesh\NepaliDate\Concerns;

use Exception;

trait HasDateManipulation
{
    /**
     * Add a specified number of days to the current date instance.
     *
     * Returns a new instance — the original is unchanged.
     *
     * @param  int  $day  The number of days to add to the current date.
     * @return static Returns a new instance with the updated date.
     *
     * @throws Exception If an error occurs during the date calculation or setup.
     */
    public function addDays(int $day): static
    {
        $totalDays = $this->dateProcessor->getDays(
            $this->date->getYear(),
            $this->date->getMonth(),
            $this->date->getDay()
        ) + $day;

        return $this->withDate($this->dateProcessor->getDateFromDays($totalDays));
    }

    /**
     * Subtract a specified number of days from the current date instance.
     *
     * Returns a new instance — the original is unchanged.
     *
     * @param  int  $day  The number of days to subtract from the current date.
     * @return static Returns a new instance with the updated date.
     *
     * @throws Exception If an error occurs during the date calculation or setup.
     */
    public function subDays(int $day): static
    {
        return $this->addDays(-$day);
    }
}
