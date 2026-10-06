<?php

declare(strict_types=1);

namespace Dipesh\NepaliDate;

use Exception;

/**
 * Class InvalidDataSetException
 *
 * Exception thrown when invalid dataset data is provided (row shape, year order, etc.).
 */
class InvalidDataSetException extends Exception
{
    /**
     * InvalidDataSetException constructor.
     *
     * @param  string  $message  Custom error message.
     * @param  int  $code  Error code.
     * @param  Exception|null  $previous  Previous exception for chaining.
     */
    public function __construct(string $message = 'Invalid dataset provided.', int $code = 0, ?Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
