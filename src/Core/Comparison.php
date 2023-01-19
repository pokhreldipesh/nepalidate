<?php

namespace Dipesh\NepaliDate\Core;

trait Comparison
{
    /**
     * Compare two dates.
     *
     * @param mixed $operator
     *
     * @return bool|int
     */
    public function compare(string $date1, string $date2, $operator = '=')
    {
        return version_compare(array_product(explode('/', $this->formatUserDate($date1))), array_product(explode('/', $this->formatUserDate($date2))), $operator);
    }

    /**
     * Check if givem date is equal to current date or not.
     *
     * @param mixed $date
     *
     * @return bool|int
     */
    public function isEqual($date)
    {
        return $this->compare($this->date, $date);
    }

    /**
     * Check if given date is greater than to current date or not.
     *
     * @param mixed $date
     *
     * @return bool|int
     */
    public function isGreaterThan($date)
    {
        return $this->compare($this->date, $date, '>');
    }

    /**
     * Check if given date is greater than or equal to current date or not.
     *
     * @param mixed $date
     *
     * @return bool|int
     */
    public function isGreaterThanOrEqual($date)
    {
        return $this->compare($this->date, $date, '>=');
    }

    /**
     * Check if given date is less than to current date or not.
     *
     * @param mixed $date
     *
     * @return bool|int
     */
    public function isLessThan($date)
    {
        return $this->compare($this->date, $date, '<');
    }

    /**
     * Check if given date is less than or equal to current date or not.
     *
     * @param mixed $date
     *
     * @return bool|int
     */
    public function isLessThanOrEqual($date)
    {
        return $this->compare($this->date, $date, '<=');
    }
}
