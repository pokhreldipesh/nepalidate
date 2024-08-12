<?php

namespace Dipesh\NepaliDate;

use Exception;

/**
 * Class InvalidDateRangeException
 *
 * Exception thrown when an invalid date range is provided.
 */
class InvalidDateRangeException extends Exception
{
    /**
     * InvalidDateRangeException constructor.
     *
     * @param  string  $message  Custom error message.
     * @param  int  $code  Error code.
     * @param  Exception|null  $previous  Previous exception for chaining.
     */
    public function __construct(string $message = 'Invalid date range provided.', int $code = 0, ?Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
