<?php

namespace Dipesh\NepaliDate\Contracts;

interface Formatter
{
    public function setUp(Date $date): static;

    public function format(string $format): string;

    public function formatNumber(int $number): string;

    public function formatMonth(string $format = 'm'): mixed;

    public function formatWeekDay(string $format = 'w'): mixed;
}
