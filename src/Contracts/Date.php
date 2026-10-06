<?php

declare(strict_types=1);

namespace Dipesh\NepaliDate\Contracts;

use Exception;

/**
 * System-level date contract.
 *
 * Provides access to date components (raw and language-aware),
 * formatting, and parsing. Implementations must hold a date's
 * year, month, day, and weekDay as accessible state.
 */
interface Date
{
    /**
     * Create a new instance with a different date, preserving language and other state.
     *
     * @throws Exception If the date format is invalid.
     */
    public function withDate(string $date): static;

    // ── Raw components (for internal calculations) ──────────────

    /**
     * Normalized date string (e.g. "2078/01/01").
     */
    public function getDate(): string;

    /**
     * Year as raw integer.
     */
    public function getYear(): int;

    /**
     * Month as raw integer (1–12).
     */
    public function getMonth(): int;

    /**
     * Day as raw integer.
     */
    public function getDay(): int;

    /**
     * Day of the week (1 = Sunday … 7 = Saturday).
     */
    public function getWeekDay(): int;

    /**
     * Current formatting language.
     */
    public function getLanguage(): Language;

    // ── Language-aware formatted components ─────────────────────

    /**
     * Year formatted for the current language.
     */
    public function year(): int|string;

    /**
     * Month formatted for the current language.
     *
     * @param  string  $format  'm' for zero-padded number, 'M' for short name, 'F' for full name.
     */
    public function month(string $format = 'm'): int|string;

    /**
     * Day formatted for the current language.
     */
    public function day(): int|string;

    // ── Formatting ──────────────────────────────────────────────
    /**
     * Format the date according to a format string.
     *
     * @param  string  $format  Format string (e.g. 'Y/m/d').
     * @param  string|Language|null  $lang  Optional language override.
     *
     * @throws Exception If the format is invalid.
     */
    public function format(string $format = 'Y/m/d', string|Language|null $lang = null): string;

    // ── Infrastructure ──────────────────────────────────────────

    /**
     * Create a Formatter instance for this date.
     */
    public function getFormatter(): Formatter;

    /**
     * Resolve a language code or instance to a Language object.
     *
     * @throws Exception If the language is not supported.
     */
    public function resolveLanguage(string|Language $language): Language;

    /**
     * Parse a date string into its year, month, and day components.
     *
     * @return array{0: int, 1: int, 2: int} Year, month, day.
     *
     * @throws Exception If the date string is invalid.
     */
    public static function parseComponents(string $date): array;
}
