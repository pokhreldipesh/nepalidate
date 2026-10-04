<?php

namespace Tests;

use Dipesh\NepaliDate\DataSet;
use Dipesh\NepaliDate\NepaliDate;
use Dipesh\NepaliDate\Services\DateProcessor;
use Dipesh\NepaliDate\SystemDataSet;
use PHPUnit\Framework\TestCase;

class DataSetIntegrationTest extends TestCase
{
    /**
     * A tiny one-year dataset: every month has 30 days (360-day year).
     *
     * @var array<int, array<int, int>>
     */
    private array $tinyRows = [
        2000 => [30, 30, 30, 30, 30, 30, 30, 30, 30, 30, 30, 30],
        2001 => [30, 30, 30, 30, 30, 30, 30, 30, 30, 30, 30, 30],
    ];

    private function tinyDataSet(): DataSet
    {
        return DataSet::make(
            $this->tinyRows,
            SystemDataSet::DEFAULT_BASE_ENGLISH_DATE,
            SystemDataSet::DEFAULT_EQUIVALENT_NEPALI_DATE,
        );
    }

    public function test_date_processor_uses_custom_dataset_rows(): void
    {
        $processor = new DateProcessor($this->tinyDataSet());

        $this->assertSame(1, $processor->getDays(2000, 1, 1));
        $this->assertSame(31, $processor->getDays(2000, 2, 1));
        $this->assertSame(361, $processor->getDays(2001, 1, 1));
    }

    public function test_date_processor_falls_back_to_packaged_lookup_table(): void
    {
        $processor = new DateProcessor;

        // Packaged table: 2000/09/17 is day 263 from the start of BS 2000,
        // and is the equivalent Nepali base date (so days-from-base is 0).
        $this->assertSame(263, $processor->getDays(2000, 9, 17));
        $this->assertSame(0, $processor->getDaysFromBase(2000, 9, 17));
        $this->assertSame(1, $processor->getDaysFromBase(2000, 9, 18));
        $this->assertNull($processor->getDataSet());
    }

    public function test_custom_dataset_exposes_rows_to_processor(): void
    {
        $processor = new DateProcessor($this->tinyDataSet());

        $this->assertInstanceOf(DataSet::class, $processor->getDataSet());
        $this->assertSame($this->tinyRows, iterator_to_array($processor->getDataSet()));
        $this->assertSame(0, $processor->getDaysFromBase(2000, 9, 17));
    }

    public function test_nepali_date_converts_using_custom_dataset(): void
    {
        // Tiny 30-day months; default base is BS 2000/09/17 === AD 1944/01/01.
        $ds = $this->tinyDataSet();
        $nepali = NepaliDate::make('2000/09/17', $ds);

        $this->assertSame('1944-01-01', $nepali->toAd()->format('Y-m-d'));
    }

    public function test_nepali_date_from_ad_date_using_custom_dataset(): void
    {
        $ds = $this->tinyDataSet();
        $nepali = NepaliDate::fromADDate('1944/01/01', $ds);

        $this->assertSame('2000/09/17', $nepali->format('Y/m/d'));
    }

    public function test_custom_dataset_round_trip(): void
    {
        $ds = $this->tinyDataSet();
        $nepali = NepaliDate::make('2001/03/15', $ds);
        $ad = $nepali->toAd();
        $back = NepaliDate::fromADDate($ad->format('Y/m/d'), $ds);

        $this->assertSame('2001/03/15', $back->format('Y/m/d'));
    }

    public function test_custom_dataset_extended_by_append(): void
    {
        $ds = SystemDataSet::packaged()
            ->append(2091, [31, 32, 31, 32, 31, 30, 30, 30, 29, 30, 29, 31]);

        $processor = new DateProcessor($ds);

        $this->assertSame(2091, $ds->lastYear());
        $this->assertTrue($processor->getDays(2091, 1, 1) > 0);

        $nepali = NepaliDate::make('2091/01/01', $ds);
        $this->assertSame(2091, $nepali->year);
    }

    public function test_default_nepali_date_behaviour_unchanged(): void
    {
        // Packaged dataset: 2000/09/17 === 1944/01/01.
        $nepali = NepaliDate::make('2000/09/17');

        $this->assertSame('1944/01/01', $nepali->toAd()->format('Y/m/d'));
        $this->assertSame('2000/09/17', NepaliDate::fromADDate('1944/01/01')->format('Y/m/d'));
    }

    public function test_nepali_date_make_without_data_set_matches_existing_api(): void
    {
        $withNull = NepaliDate::make('2081-4-25');
        $legacy = new NepaliDate('2081-4-25');

        $this->assertSame($legacy->format('Y/m/d'), $withNull->format('Y/m/d'));
    }

    public function test_custom_base_dates_drive_conversion(): void
    {
        // Tiny 30-day year with a custom epoch: BS 2000/01/01 === AD 2000/01/01.
        $ds = DataSet::make($this->tinyRows, '2000/01/01', '2000/01/01');

        $nepali = NepaliDate::make('2000/01/01', $ds);
        $this->assertSame('2000-01-01', $nepali->toAd()->format('Y-m-d'));

        $nepali = NepaliDate::make('2000/02/01', $ds);
        $this->assertSame('2000-01-31', $nepali->toAd()->format('Y-m-d'));
    }

    public function test_subclass_dataset_works_with_date_processor(): void
    {
        $ds = new TinyCustomDataSet; // 30-day months, BS 2000/01/01 === AD 2000/01/01
        $processor = new DateProcessor($ds);

        $this->assertInstanceOf(TinyCustomDataSet::class, $processor->getDataSet());
        $this->assertSame(1, $processor->getDays(2000, 1, 1));
        $this->assertSame(31, $processor->getDays(2000, 2, 1));
        $this->assertSame(0, $processor->getDaysFromBase(2000, 1, 1));
    }

    public function test_subclass_dataset_works_with_nepali_date(): void
    {
        $ds = new TinyCustomDataSet;

        $nepali = NepaliDate::make('2000/02/01', $ds);
        $this->assertSame('2000-01-31', $nepali->toAd()->format('Y-m-d'));

        $back = NepaliDate::fromADDate('2000/01/31', $ds);
        $this->assertSame('2000/02/01', $back->format('Y/m/d'));
    }

    public function test_subclass_dataset_round_trip(): void
    {
        $ds = new TinyCustomDataSet([2000 => $this->tinyRows[2000], 2001 => $this->tinyRows[2001]]);

        $nepali = NepaliDate::make('2001/03/15', $ds);
        $ad = $nepali->toAd();
        $back = NepaliDate::fromADDate($ad->format('Y/m/d'), $ds);

        $this->assertSame('2001/03/15', $back->format('Y/m/d'));
    }

    public function test_system_data_set_defaults(): void
    {
        $ds = new SystemDataSet;

        $this->assertSame(SystemDataSet::DEFAULT_BASE_ENGLISH_DATE, $ds->getBaseEnglishDate());
        $this->assertSame(SystemDataSet::DEFAULT_EQUIVALENT_NEPALI_DATE, $ds->getEquivalentNepaliDate());
        $this->assertTrue($ds->isEmpty());
    }

    public function test_system_data_set_make_keeps_system_base_dates(): void
    {
        $ds = SystemDataSet::make([2000 => $this->tinyRows[2000]]);

        $this->assertInstanceOf(SystemDataSet::class, $ds);
        $this->assertSame(SystemDataSet::DEFAULT_BASE_ENGLISH_DATE, $ds->getBaseEnglishDate());
        $this->assertSame(SystemDataSet::DEFAULT_EQUIVALENT_NEPALI_DATE, $ds->getEquivalentNepaliDate());
    }

    public function test_system_data_set_packaged_matches_default_behaviour(): void
    {
        $packaged = SystemDataSet::packaged();

        $this->assertSame(2000, $packaged->firstYear());
        $this->assertSame(2090, $packaged->lastYear());
        $this->assertSame(91, $packaged->count());

        $viaDefault = NepaliDate::make('2000/09/17');
        $viaPackaged = NepaliDate::make('2000/09/17', $packaged);

        $this->assertSame($viaDefault->format('Y/m/d'), $viaPackaged->format('Y/m/d'));
        $this->assertSame('1944/01/01', $viaPackaged->toAd()->format('Y/m/d'));
    }
}
