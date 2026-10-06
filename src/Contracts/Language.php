<?php

declare(strict_types=1);

namespace Dipesh\NepaliDate\Contracts;

/**
 * Language pack contract.
 *
 * Provides localized digits, weekday names, month names, and
 * the half-moon (gate) indicator for BS date formatting.
 * Implementations should hold immutable data (class constants).
 */
interface Language
{
    /**
     * Localized label for the "gate" (half-moon) indicator.
     */
    public function getGate(): string;

    /**
     * Convert a single Arabic digit (0–9) to the language's digit character.
     */
    public function getDigit(int $digit): int|string;

    /**
     * Get localized weekday names for a weekday index.
     *
     * @param  int  $week  Zero-based weekday index (0 = Sunday … 6 = Saturday).
     * @return array{l: string, D: string} Long name ('l') and short name ('D').
     */
    public function getWeek(int $week): array;

    /**
     * Get localized month names for a month index.
     *
     * @param  int  $month  Zero-based month index (0 = Baisakh … 11 = Chaitra).
     * @return array{F: string, M: string} Full name ('F') and short name ('M').
     */
    public function getMonth(int $month): array;
}
