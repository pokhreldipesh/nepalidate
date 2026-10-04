<?php

namespace Dipesh\NepaliDate;

use Dipesh\NepaliDate\Concerns\HasDateComparison;
use Dipesh\NepaliDate\Concerns\HasDateConversion;
use Dipesh\NepaliDate\Concerns\HasDateManipulation;
use Dipesh\NepaliDate\Concerns\HasDateOperation;
use Dipesh\NepaliDate\Contracts\DateProcessor as DateProcessorContract;
use Dipesh\NepaliDate\Contracts\Language;
use Dipesh\NepaliDate\lang\English;
use Dipesh\NepaliDate\Services\Date;
use Dipesh\NepaliDate\Services\DateProcessor;
use Exception;

/**
 * NepaliDate Class
 *
 * Public API for working with Nepali (BS) dates. Extends the system-level
 * Date value object with calendar-aware operations: conversion, manipulation,
 * comparison, and weekday resolution.
 */
class NepaliDate extends Date
{
    use HasDateComparison, HasDateConversion, HasDateManipulation, HasDateOperation;

    /**
     * @var DataSet|null Optional custom calendar dataset used for conversion.
     */
    public ?DataSet $dataSet;

    /**
     * @var DateProcessorContract Day-math engine over the calendar dataset.
     */
    public DateProcessorContract $dateProcessor;

    /**
     * @param  string|null  $date  Date string in Nepali format. Defaults to current date.
     * @param  Language|null  $language  Formatting language. Defaults to English.
     * @param  DataSet|null  $dataSet  Optional custom calendar dataset.
     *
     * @throws Exception
     */
    public function __construct(?string $date = null, ?Language $language = null, ?DataSet $dataSet = null)
    {
        $this->dataSet = $dataSet;
        $this->dateProcessor = $this->getDateProcessor();

        parent::__construct($date ?? self::now($dataSet)->date, $language ?? new English);

        $this->computeWeekDay();
    }

    /**
     * Create a DateProcessor for the current dataset.
     */
    public function getDateProcessor(): DateProcessorContract
    {
        return new DateProcessor($this->dataSet);
    }

    /**
     * Compute and assign the weekDay property from the current date components.
     */
    protected function computeWeekDay(): void
    {
        $this->weekDay = $this->dateProcessor->getWeekDayFromDays(
            $this->getTotalDaysFromBaseDate($this->date)
        );
    }

    /**
     * Re-parse the date and refresh weekDay.
     *
     * @throws Exception
     */
    public function setUp(string $date): void
    {
        parent::setUp($date);
        $this->computeWeekDay();
    }

    /**
     * Get Current Date
     *
     * @param  DataSet|null  $dataSet  Optional custom calendar dataset.
     *
     * @throws Exception
     */
    public static function now(?DataSet $dataSet = null): static
    {
        return self::fromADDate((new EnDate)->format('Y-m-d'), $dataSet);
    }

    /**
     * Create a New Instance with a Given Date
     *
     * @param  string  $date  Date string in Nepali format.
     *
     * @throws Exception
     */
    public function create(string $date): static
    {
        $newDateInstance = clone $this;
        $newDateInstance->setUp($date);

        return $newDateInstance;
    }

    /**
     * Create a New Instance
     *
     * @param  string  $date  Date string in Nepali format.
     * @param  DataSet|null  $dataSet  Optional custom calendar dataset.
     *
     * @throws Exception
     */
    public static function make(string $date, ?DataSet $dataSet = null): self
    {
        return new static($date, null, $dataSet);
    }

    /**
     * Set Default Formatting Language
     *
     * @param  string|Language  $language  Language code or instance.
     *
     * @throws Exception
     */
    public function setLang(string|Language $language): static
    {
        $instance = clone $this;
        $instance->language = $this->resolveLanguage($language);
        $instance->formatter = clone $instance->formatter->setUp($instance);

        return $instance;
    }

    /**
     * Convert to String
     */
    public function __toString(): string
    {
        return $this->date;
    }
}
