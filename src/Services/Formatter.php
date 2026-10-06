<?php

namespace Dipesh\NepaliDate\Services;

use Dipesh\NepaliDate\Contracts\Date;
use Dipesh\NepaliDate\Contracts\Language;
use Exception;

/**
 * Base class for formatters.
 *
 * Receives a Date on construction and reads its state live —
 * no setUp or re-binding needed. Subclasses implement format(),
 * formatMonth(), and formatWeekDay().
 */
abstract class Formatter implements \Dipesh\NepaliDate\Contracts\Formatter
{
    /**
     * @var string[] Format characters accepted by format().
     */
    protected array $supportedFormats = ['Y', 'm', 'M', 'F', 'd', 'w', 'D', 'l', 'g'];

    public function __construct(protected Date $date) {}

    /**
     * Convert Arabic digits to the language-specific digit set.
     */
    public function formatNumber(int|string $number): string
    {
        return (string) preg_replace_callback('/\d/m', fn ($matches): string => (string) $this->language()->getDigit((int) $matches[0]), (string) $number);
    }

    /**
     * The date's current formatting language.
     */
    protected function language(): Language
    {
        return $this->date->getLanguage();
    }

    /**
     * Validate that a format string contains only supported characters.
     *
     * @throws Exception If unsupported format characters are present.
     */
    protected function validateSupportedFormats(string $format): void
    {
        preg_match_all('/\w*/m', $format, $matches);

        $unsupported = array_diff(array_filter($matches[0]), $this->supportedFormats);

        if ($unsupported !== []) {
            throw new Exception('Invalid date format');
        }
    }

    abstract public function format(string $format): string;

    abstract public function formatMonth(string $format = 'm'): mixed;

    abstract public function formatWeekDay(string $format = 'w'): mixed;
}
