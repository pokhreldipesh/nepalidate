<?php

namespace Dipesh\NepaliDate\Contracts;

interface Language
{
    public function getGate(): string;

    public function getDigit(int $digit): int|string;

    public function getWeek(int $week): array;

    public function getMonth(int $month): array;
}
