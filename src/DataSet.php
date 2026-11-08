<?php

namespace Dipesh\NepaliDate;

use ArrayIterator;
use IteratorAggregate;
use Serializable;

/**
 * DataSet
 *
 * Generic, fluent, mutable container for calendar lookup data: year rows
 * (year => 12 month day-counts) plus the base English date and equivalent
 * Nepali date used for date conversion. Iterable and serializable.
 *
 * Initialize with your own rows, or extend this class to ship a custom dataset.
 * The packaged system calendar lives on SystemDataSet.
 */
/**
 * @implements IteratorAggregate<int, array<int, int>>
 *
 * @phpstan-consistent-constructor
 */
class DataSet implements IteratorAggregate, Serializable
{
    /**
     * Year rows: year => list of 12 month day-counts (29-32).
     *
     * @var array<int, array<int, int>>
     */
    protected array $rows = [];

    protected string $baseEnglishDate = '';

    protected string $equivalentNepaliDate = '';

    /**
     * @param  array<int, array<int, int>>  $rows  year => 12 month day-counts
     * @param  string|null  $baseEnglishDate  AD base date (Y/m/d); defaults to empty
     * @param  string|null  $equivalentNepaliDate  BS base date (Y/m/d); defaults to empty
     */
    public function __construct(array $rows = [], ?string $baseEnglishDate = null, ?string $equivalentNepaliDate = null)
    {
        $this->addRows($rows);

        if ($baseEnglishDate !== null) {
            $this->baseEnglishDate = $baseEnglishDate;
        }

        if ($equivalentNepaliDate !== null) {
            $this->equivalentNepaliDate = $equivalentNepaliDate;
        }
    }

    /**
     * Create a new DataSet, optionally seeded with year rows.
     *
     * @param  array<int, array<int, int>>  $rows
     */
    public static function make(array $rows = [], ?string $baseEnglishDate = null, ?string $equivalentNepaliDate = null): static
    {
        return new static($rows, $baseEnglishDate, $equivalentNepaliDate);
    }

    /**
     * @return ArrayIterator<int, array<int, int>>
     */
    public function getIterator(): ArrayIterator
    {
        return new ArrayIterator($this->rows);
    }

    /**
     * @return array{rows: array<int, array<int, int>>, baseEnglishDate: string, equivalentNepaliDate: string}
     */
    public function __serialize(): array
    {
        return [
            'rows' => $this->rows,
            'baseEnglishDate' => $this->baseEnglishDate,
            'equivalentNepaliDate' => $this->equivalentNepaliDate,
        ];
    }

    /**
     * @param  array{rows?: array<int, array<int, int>>, baseEnglishDate?: string, equivalentNepaliDate?: string}  $data
     */
    public function __unserialize(array $data): void
    {
        $this->rows = [];
        $this->addRows($data['rows'] ?? []);
        $this->baseEnglishDate = (string) ($data['baseEnglishDate'] ?? '');
        $this->equivalentNepaliDate = (string) ($data['equivalentNepaliDate'] ?? '');
    }

    public function serialize(): string
    {
        return serialize($this->__serialize());
    }

    public function unserialize(string $data): void
    {
        $this->__unserialize(unserialize($data));
    }

    /**
     * Upsert a single year row (overwrites if the year already exists).
     *
     * @param  array<int, int>  $monthDays  exactly 12 day-counts
     */
    public function addRow(int $year, array $monthDays): self
    {
        $this->assertValidRow($year, $monthDays);

        $this->rows[$year] = array_values($monthDays);
        ksort($this->rows, SORT_NUMERIC);

        return $this;
    }

    /**
     * Upsert a map of year rows.
     *
     * @param  array<int, array<int, int>>  $rows
     */
    public function addRows(array $rows): self
    {
        foreach ($rows as $year => $monthDays) {
            $this->addRow((int) $year, $monthDays);
        }

        return $this;
    }

    /**
     * Insert a year row strictly after the current last year.
     * On an empty dataset behaves like addRow().
     *
     * @param  array<int, int>  $monthDays
     */
    public function append(int $year, array $monthDays): self
    {
        $last = $this->lastYear();

        if ($last !== null) {
            if ($year <= $last) {
                throw new InvalidDataSetException("Append year must be after last year ({$last}).");
            }

            if (isset($this->rows[$year])) {
                throw new InvalidDataSetException("Year {$year} already exists in the dataset.");
            }
        }

        return $this->addRow($year, $monthDays);
    }

    /**
     * Append multiple year rows. Every year must be after the current last year.
     *
     * @param  array<int, array<int, int>>  $rows
     */
    public function appendRows(array $rows): self
    {
        $last = $this->lastYear();
        $seen = [];

        foreach ($rows as $year => $monthDays) {
            $year = (int) $year;

            if (isset($seen[$year])) {
                throw new InvalidDataSetException("Duplicate year {$year} in batch.");
            }

            if ($last !== null && $year <= $last) {
                throw new InvalidDataSetException("Append year must be after last year ({$last}).");
            }

            if (isset($this->rows[$year])) {
                throw new InvalidDataSetException("Year {$year} already exists in the dataset.");
            }

            $seen[$year] = true;
            $this->assertValidRow($year, $monthDays);
        }

        foreach ($rows as $year => $monthDays) {
            $this->addRow((int) $year, $monthDays);
        }

        return $this;
    }

    /**
     * Insert a year row strictly before the current first year.
     * On an empty dataset behaves like addRow().
     *
     * @param  array<int, int>  $monthDays
     */
    public function prepend(int $year, array $monthDays): self
    {
        $first = $this->firstYear();

        if ($first !== null) {
            if ($year >= $first) {
                throw new InvalidDataSetException("Prepend year must be before first year ({$first}).");
            }

            if (isset($this->rows[$year])) {
                throw new InvalidDataSetException("Year {$year} already exists in the dataset.");
            }
        }

        return $this->addRow($year, $monthDays);
    }

    /**
     * Prepend multiple year rows. Every year must be before the current first year.
     *
     * @param  array<int, array<int, int>>  $rows
     */
    public function prependRows(array $rows): self
    {
        $first = $this->firstYear();
        $seen = [];

        foreach ($rows as $year => $monthDays) {
            $year = (int) $year;

            if (isset($seen[$year])) {
                throw new InvalidDataSetException("Duplicate year {$year} in batch.");
            }

            if ($first !== null && $year >= $first) {
                throw new InvalidDataSetException("Prepend year must be before first year ({$first}).");
            }

            if (isset($this->rows[$year])) {
                throw new InvalidDataSetException("Year {$year} already exists in the dataset.");
            }

            $seen[$year] = true;
            $this->assertValidRow($year, $monthDays);
        }

        foreach ($rows as $year => $monthDays) {
            $this->addRow((int) $year, $monthDays);
        }

        return $this;
    }

    /**
     * Validate a year row: year > 0, exactly 12 ints with day-counts 29-32.
     *
     * @param  array<int, mixed>  $monthDays
     */
    private function assertValidRow(int $year, array $monthDays): void
    {
        if ($year <= 0) {
            throw new InvalidDataSetException("Year must be a positive integer, got {$year}.");
        }

        if (count($monthDays) !== 12 || array_keys($monthDays) !== range(0, 11)) {
            throw new InvalidDataSetException("Year {$year} must have exactly 12 month day-counts.");
        }

        foreach ($monthDays as $index => $days) {
            if (! is_int($days) || $days < 29 || $days > 32) {
                throw new InvalidDataSetException(
                    "Year {$year} month index {$index} day-count must be an integer between 29 and 32."
                );
            }
        }
    }

    /**
     * @return array<int, int>
     */
    public function years(): array
    {
        return array_keys($this->rows);
    }

    public function firstYear(): ?int
    {
        if ($this->rows === []) {
            return null;
        }

        return array_key_first($this->rows);
    }

    public function lastYear(): ?int
    {
        if ($this->rows === []) {
            return null;
        }

        return array_key_last($this->rows);
    }

    public function count(): int
    {
        return count($this->rows);
    }

    public function isEmpty(): bool
    {
        return $this->rows === [];
    }

    public function getBaseEnglishDate(): string
    {
        return $this->baseEnglishDate;
    }

    public function getEquivalentNepaliDate(): string
    {
        return $this->equivalentNepaliDate;
    }
}
