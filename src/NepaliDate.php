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
use Stringable;

/**
 * NepaliDate Class
 *
 * Public API for working with Nepali (BS) dates. Extends the system-level
 * Date value object with calendar-aware operations: conversion, manipulation,
 * comparison, and weekday resolution.
 */
/**
 * @phpstan-consistent-constructor
 */
class NepaliDate extends Date implements Stringable
{
    use HasDateComparison, HasDateConversion, HasDateManipulation, HasDateOperation;

    /**
     * Day-math engine over the calendar dataset.
     */
    public private(set) DateProcessorContract $dateProcessor;

    /**
     * @param  string|null  $date  Date string in Nepali format. Defaults to current date.
     * @param  Language|null  $language  Formatting language. Defaults to English.
     * @param  DataSet|null  $dataSet  Optional custom calendar dataset.
     *
     * @throws Exception
     */
    public function __construct(?string $date = null, ?Language $language = null, public ?DataSet $dataSet = null)
    {
        $this->dateProcessor = $this->getDateProcessor();

        parent::__construct($date ?? self::now($this->dataSet)->date, $language ?? new English);

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
     * Create a new instance with a different date, preserving language and dataset.
     *
     * @throws Exception
     */
    public function withDate(string $date): static
    {
        return new static($date, $this->language, $this->dataSet);
    }

    /**
     * Mutate this instance to a new date and refresh weekDay.
     *
     * @throws Exception
     */
    public function setUp(string $date): void
    {
        $this->assignDate($date);
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
        return $this->withDate($date);
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
        $instance->formatter = $instance->getFormatter();

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
