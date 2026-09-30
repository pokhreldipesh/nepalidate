<?php

namespace Tests;

use Dipesh\NepaliDate\DataSet;
use Dipesh\NepaliDate\InvalidDataSetException;
use PHPUnit\Framework\TestCase;

class DataSetTest extends TestCase
{
    private array $validRow = [31, 32, 31, 32, 31, 30, 30, 30, 29, 30, 29, 31];

    private array $validRow2 = [31, 31, 32, 31, 31, 31, 30, 29, 30, 29, 30, 30];

    public function test_make_empty_dataset(): void
    {
        $ds = DataSet::make();

        $this->assertSame(0, $ds->count());
        $this->assertTrue($ds->isEmpty());
        $this->assertNull($ds->firstYear());
        $this->assertNull($ds->lastYear());
        $this->assertSame([], iterator_to_array($ds));
        $this->assertSame('', $ds->getBaseEnglishDate());
        $this->assertSame('', $ds->getEquivalentNepaliDate());
    }

    public function test_make_seeded_from_rows(): void
    {
        $ds = DataSet::make([2000 => $this->validRow]);

        $this->assertSame(1, $ds->count());
        $this->assertSame($this->validRow, iterator_to_array($ds)[2000]);
    }

    public function test_add_row_and_add_rows_upsert(): void
    {
        $ds = DataSet::make()
            ->addRow(2000, $this->validRow)
            ->addRow(2001, $this->validRow2);

        $rows = iterator_to_array($ds);
        $this->assertSame($this->validRow, $rows[2000]);

        $replacement = [29, 29, 29, 29, 29, 29, 29, 29, 29, 29, 29, 29];
        $ds->addRow(2000, $replacement);

        $rows = iterator_to_array($ds);
        $this->assertSame($replacement, $rows[2000]);
        $this->assertSame(2, $ds->count());

        $ds->addRows([2002 => $this->validRow, 2003 => $this->validRow2]);
        $this->assertSame(4, $ds->count());
        $this->assertSame([2000, 2001, 2002, 2003], $ds->years());
    }

    public function test_rows_stay_sorted_by_year(): void
    {
        $ds = DataSet::make()
            ->addRow(2005, $this->validRow)
            ->addRow(2001, $this->validRow)
            ->addRow(2003, $this->validRow);

        $this->assertSame([2001, 2003, 2005], $ds->years());
    }

    public function test_append_single_and_many(): void
    {
        $ds = DataSet::make([2000 => $this->validRow])
            ->append(2001, $this->validRow2)
            ->appendRows([2002 => $this->validRow, 2003 => $this->validRow2]);

        $this->assertSame([2000, 2001, 2002, 2003], $ds->years());
    }

    public function test_prepend_single_and_many(): void
    {
        $ds = DataSet::make([2000 => $this->validRow])
            ->prepend(1999, $this->validRow2)
            ->prependRows([1997 => $this->validRow, 1998 => $this->validRow2]);

        $this->assertSame([1997, 1998, 1999, 2000], $ds->years());
    }

    public function test_append_to_empty(): void
    {
        $ds = DataSet::make()->append(2000, $this->validRow);

        $this->assertSame([2000], $ds->years());
    }

    public function test_prepend_to_empty(): void
    {
        $ds = DataSet::make()->prepend(2000, $this->validRow);

        $this->assertSame([2000], $ds->years());
    }

    public function test_append_rejects_year_not_after_last(): void
    {
        $ds = DataSet::make([2005 => $this->validRow]);

        $this->expectException(InvalidDataSetException::class);
        $ds->append(2004, $this->validRow);
    }

    public function test_prepend_rejects_year_not_before_first(): void
    {
        $ds = DataSet::make([2005 => $this->validRow]);

        $this->expectException(InvalidDataSetException::class);
        $ds->prepend(2006, $this->validRow);
    }

    public function test_append_rejects_duplicate_year(): void
    {
        $ds = DataSet::make([2005 => $this->validRow, 2006 => $this->validRow]);

        $this->expectException(InvalidDataSetException::class);
        $ds->append(2005, $this->validRow);
    }

    public function test_prepend_rejects_duplicate_year(): void
    {
        $ds = DataSet::make([2005 => $this->validRow, 2006 => $this->validRow]);

        $this->expectException(InvalidDataSetException::class);
        $ds->prepend(2006, $this->validRow);
    }

    public function test_add_row_rejects_invalid_month_count(): void
    {
        $ds = DataSet::make();

        $this->expectException(InvalidDataSetException::class);
        $ds->addRow(2000, [31, 32, 31, 32, 31, 30, 30, 30, 29, 30, 29]);
    }

    public function test_add_row_rejects_day_out_of_range(): void
    {
        $ds = DataSet::make();

        $this->expectException(InvalidDataSetException::class);
        $ds->addRow(2000, [28, 32, 31, 32, 31, 30, 30, 30, 29, 30, 29, 31]);
    }

    public function test_add_row_rejects_non_int_day(): void
    {
        $ds = DataSet::make();

        $this->expectException(InvalidDataSetException::class);
        $ds->addRow(2000, [31, 32, 31, 32, 31, 30, 30, 30, 29, 30, 29, '31']);
    }

    public function test_is_iterable_by_year(): void
    {
        $ds = DataSet::make([2000 => $this->validRow, 2001 => $this->validRow2]);

        $seen = [];
        foreach ($ds as $year => $monthDays) {
            $seen[$year] = $monthDays;
        }

        $this->assertSame([2000 => $this->validRow, 2001 => $this->validRow2], $seen);
    }

    public function test_serialize_round_trip(): void
    {
        $original = DataSet::make([2001 => $this->validRow2]);

        $restored = unserialize(serialize($original));

        $this->assertInstanceOf(DataSet::class, $restored);
        $this->assertSame(iterator_to_array($original), iterator_to_array($restored));
        $this->assertSame($original->getBaseEnglishDate(), $restored->getBaseEnglishDate());
        $this->assertSame($original->getEquivalentNepaliDate(), $restored->getEquivalentNepaliDate());
    }

    public function test_serializable_interface_round_trip(): void
    {
        $original = DataSet::make([2000 => $this->validRow]);

        $restored = new DataSet;
        $restored->unserialize($original->serialize());

        $this->assertSame(iterator_to_array($original), iterator_to_array($restored));
    }

    public function test_mutators_are_fluent_and_mutable(): void
    {
        $ds = DataSet::make();

        $returned = $ds
            ->addRow(2000, $this->validRow)
            ->append(2001, $this->validRow2);

        $this->assertSame($ds, $returned);
        $this->assertSame(2, $ds->count());
    }

    public function test_non_sequential_years_supported(): void
    {
        $ds = DataSet::make()
            ->addRow(2000, $this->validRow)
            ->addRow(2005, $this->validRow2);

        $this->assertSame([2000, 2005], $ds->years());
    }

    public function test_count_years_and_empty_transitions(): void
    {
        $ds = DataSet::make()
            ->addRow(2000, $this->validRow)
            ->addRow(2001, $this->validRow2);

        $this->assertSame(2, $ds->count());

        $empty = DataSet::make();

        $this->assertTrue($empty->isEmpty());
        $this->assertSame(0, $empty->count());
    }

    public function test_constructor_accepts_custom_rows_and_base_dates(): void
    {
        $ds = new DataSet([2000 => $this->validRow], '1944/01/01', '2000/09/17');

        $this->assertSame([2000], $ds->years());
        $this->assertSame('1944/01/01', $ds->getBaseEnglishDate());
        $this->assertSame('2000/09/17', $ds->getEquivalentNepaliDate());
    }

    public function test_make_accepts_custom_rows_and_base_dates(): void
    {
        $ds = DataSet::make([2001 => $this->validRow2], '1945-2-3', '2001-10-5');

        $this->assertSame([2001], $ds->years());
        $this->assertSame('1945-2-3', $ds->getBaseEnglishDate());
        $this->assertSame('2001-10-5', $ds->getEquivalentNepaliDate());
    }

    public function test_make_is_late_static_bound(): void
    {
        $ds = TinyCustomDataSet::make();

        $this->assertInstanceOf(TinyCustomDataSet::class, $ds);
        $this->assertSame([2000], $ds->years());
        $this->assertSame('2000/01/01', $ds->getBaseEnglishDate());
        $this->assertSame('2000/01/01', $ds->getEquivalentNepaliDate());
    }

    public function test_subclass_can_override_defaults(): void
    {
        $ds = new TinyCustomDataSet;

        $this->assertInstanceOf(DataSet::class, $ds);
        $this->assertSame([2000], $ds->years());
        $this->assertSame('2000/01/01', $ds->getBaseEnglishDate());
    }

    public function test_subclass_can_override_base_dates_explicitly(): void
    {
        $ds = new TinyCustomDataSet([2000 => $this->validRow], '1999/12/31', '2000/01/01');

        $this->assertSame('1999/12/31', $ds->getBaseEnglishDate());
        $this->assertSame($this->validRow, iterator_to_array($ds)[2000]);
    }

    public function test_subclass_fluent_mutators_return_same_instance(): void
    {
        $ds = new TinyCustomDataSet;

        $returned = $ds->addRow(2001, $this->validRow2);

        $this->assertSame($ds, $returned);
        $this->assertInstanceOf(TinyCustomDataSet::class, $returned);
        $this->assertSame([2000, 2001], $ds->years());
    }

    public function test_custom_base_dates_survive_serialization(): void
    {
        $original = DataSet::make([2000 => $this->validRow], '1944/01/01', '2000/09/17');

        $restored = unserialize(serialize($original));

        $this->assertSame('1944/01/01', $restored->getBaseEnglishDate());
        $this->assertSame('2000/09/17', $restored->getEquivalentNepaliDate());
        $this->assertSame(iterator_to_array($original), iterator_to_array($restored));
    }

    public function test_subclass_serializes_and_restores_as_subclass(): void
    {
        $original = new TinyCustomDataSet;

        $restored = unserialize(serialize($original));

        $this->assertInstanceOf(TinyCustomDataSet::class, $restored);
        $this->assertSame($original->getBaseEnglishDate(), $restored->getBaseEnglishDate());
        $this->assertSame(iterator_to_array($original), iterator_to_array($restored));
    }
}
