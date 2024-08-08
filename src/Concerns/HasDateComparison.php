<?php

namespace Dipesh\NepaliDate\Concerns;

use Dipesh\NepaliDate\Contracts\Date;
use Exception;

trait HasDateComparison
{
    /**
     * @throws Exception
     */
    public function isEqual(Date|string $date): bool
    {
       return $this->getTotalDaysFromBaseDate($this->date) == $this->getTotalDaysFromBaseDate($date);
    }

    /**
     * @param Date|string $date
     * @return bool
     * @throws Exception
     */
    public function isGreaterThan(Date|string $date): bool
    {
        return $this->getTotalDaysFromBaseDate($this->date) > $this->getTotalDaysFromBaseDate($date);
    }

    /**
     * @throws Exception
     */
    public function isLessThan(Date|string $date): bool
    {
        return $this->getTotalDaysFromBaseDate($this->date) < $this->getTotalDaysFromBaseDate($date);
    }
}
