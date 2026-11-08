<?php

declare(strict_types=1);

use Dipesh\NepaliDate\DataSet;
use Dipesh\NepaliDate\InvalidDataSetException;
use Tests\TinyCustomDataSet;

beforeEach(function (): void {
    $this->validRow = [31, 32, 31, 32, 31, 30, 30, 30, 29, 30, 29, 31];
    $this->validRow2 = [31, 31, 32, 31, 31, 31, 30, 29, 30, 29, 30, 30];
});

describe('DataSet creation', function (): void {
    it('creates empty dataset', function (): void {
        $ds = DataSet::make();

        expect($ds->count())->toBe(0)
            ->and($ds->isEmpty())->toBeTrue()
            ->and($ds->firstYear())->toBeNull()
            ->and($ds->lastYear())->toBeNull()
            ->and(iterator_to_array($ds))->toBe([])
            ->and($ds->getBaseEnglishDate())->toBe('')
            ->and($ds->getEquivalentNepaliDate())->toBe('');
    });

    it('creates from seeded rows', function (): void {
        $ds = DataSet::make([2000 => $this->validRow]);

        expect($ds->count())->toBe(1)
            ->and(iterator_to_array($ds)[2000])->toBe($this->validRow);
    });

    it('accepts custom base dates in constructor', function (): void {
        $ds = new DataSet([2000 => $this->validRow], '1944/01/01', '2000/09/17');

        expect($ds->years())->toBe([2000])
            ->and($ds->getBaseEnglishDate())->toBe('1944/01/01')
            ->and($ds->getEquivalentNepaliDate())->toBe('2000/09/17');
    });

    it('accepts custom base dates in make()', function (): void {
        $ds = DataSet::make([2001 => $this->validRow2], '1945-2-3', '2001-10-5');

        expect($ds->years())->toBe([2001])
            ->and($ds->getBaseEnglishDate())->toBe('1945-2-3')
            ->and($ds->getEquivalentNepaliDate())->toBe('2001-10-5');
    });
});

describe('Row mutators', function (): void {
    it('addRow and addRows upsert', function (): void {
        $ds = DataSet::make()
            ->addRow(2000, $this->validRow)
            ->addRow(2001, $this->validRow2);

        $replacement = [29, 29, 29, 29, 29, 29, 29, 29, 29, 29, 29, 29];
        $ds->addRow(2000, $replacement);

        expect(iterator_to_array($ds)[2000])->toBe($replacement)
            ->and($ds->count())->toBe(2);

        $ds->addRows([2002 => $this->validRow, 2003 => $this->validRow2]);

        expect($ds->count())->toBe(4)
            ->and($ds->years())->toBe([2000, 2001, 2002, 2003]);
    });

    it('keeps rows sorted by year', function (): void {
        $ds = DataSet::make()
            ->addRow(2005, $this->validRow)
            ->addRow(2001, $this->validRow)
            ->addRow(2003, $this->validRow);

        expect($ds->years())->toBe([2001, 2003, 2005]);
    });

    it('appends single and multiple rows', function (): void {
        $ds = DataSet::make([2000 => $this->validRow])
            ->append(2001, $this->validRow2)
            ->appendRows([2002 => $this->validRow, 2003 => $this->validRow2]);

        expect($ds->years())->toBe([2000, 2001, 2002, 2003]);
    });

    it('prepends single and multiple rows', function (): void {
        $ds = DataSet::make([2000 => $this->validRow])
            ->prepend(1999, $this->validRow2)
            ->prependRows([1997 => $this->validRow, 1998 => $this->validRow2]);

        expect($ds->years())->toBe([1997, 1998, 1999, 2000]);
    });

    it('appends to empty dataset', function (): void {
        expect(DataSet::make()->append(2000, $this->validRow)->years())->toBe([2000]);
    });

    it('prepends to empty dataset', function (): void {
        expect(DataSet::make()->prepend(2000, $this->validRow)->years())->toBe([2000]);
    });

    it('mutators are fluent', function (): void {
        $ds = DataSet::make();
        $returned = $ds->addRow(2000, $this->validRow)->append(2001, $this->validRow2);

        expect($returned)->toBe($ds)
            ->and($ds->count())->toBe(2);
    });

    it('supports non-sequential years', function (): void {
        $ds = DataSet::make()
            ->addRow(2000, $this->validRow)
            ->addRow(2005, $this->validRow2);

        expect($ds->years())->toBe([2000, 2005]);
    });
});

describe('Row validation', function (): void {
    it('rejects append year not after last', function (): void {
        DataSet::make([2005 => $this->validRow])->append(2004, $this->validRow);
    })->throws(InvalidDataSetException::class);

    it('rejects prepend year not before first', function (): void {
        DataSet::make([2005 => $this->validRow])->prepend(2006, $this->validRow);
    })->throws(InvalidDataSetException::class);

    it('rejects invalid month count', function (): void {
        DataSet::make()->addRow(2000, [31, 32, 31, 32, 31, 30, 30, 30, 29, 30, 29]);
    })->throws(InvalidDataSetException::class);

    it('rejects day count out of range', function (): void {
        DataSet::make()->addRow(2000, [28, 32, 31, 32, 31, 30, 30, 30, 29, 30, 29, 31]);
    })->throws(InvalidDataSetException::class);

    it('rejects non-integer day count', function (): void {
        DataSet::make()->addRow(2000, [31, 32, 31, 32, 31, 30, 30, 30, 29, 30, 29, '31']);
    })->throws(InvalidDataSetException::class);
});

describe('Iteration & serialization', function (): void {
    it('iterates by year', function (): void {
        $ds = DataSet::make([2000 => $this->validRow, 2001 => $this->validRow2]);

        $seen = [];
        foreach ($ds as $year => $monthDays) {
            $seen[$year] = $monthDays;
        }

        expect($seen)->toBe([2000 => $this->validRow, 2001 => $this->validRow2]);
    });

    it('round-trips through serialize/unserialize', function (): void {
        $original = DataSet::make([2001 => $this->validRow2], '1944/01/01', '2000/09/17');

        $restored = unserialize(serialize($original));

        expect($restored)->toBeInstanceOf(DataSet::class)
            ->and(iterator_to_array($restored))->toBe(iterator_to_array($original))
            ->and($restored->getBaseEnglishDate())->toBe('1944/01/01')
            ->and($restored->getEquivalentNepaliDate())->toBe('2000/09/17');
    });
});

describe('Subclass support', function (): void {
    it('make() is late-static-bound', function (): void {
        $ds = TinyCustomDataSet::make();

        expect($ds)->toBeInstanceOf(TinyCustomDataSet::class)
            ->and($ds->years())->toBe([2000])
            ->and($ds->getBaseEnglishDate())->toBe('2000/01/01');
    });

    it('subclass can override defaults', function (): void {
        $ds = new TinyCustomDataSet;

        expect($ds)->toBeInstanceOf(DataSet::class)
            ->and($ds->years())->toBe([2000])
            ->and($ds->getBaseEnglishDate())->toBe('2000/01/01');
    });

    it('subclass fluent mutators return same instance', function (): void {
        $ds = new TinyCustomDataSet;
        $returned = $ds->addRow(2001, $this->validRow2);

        expect($returned)->toBe($ds)
            ->and($returned)->toBeInstanceOf(TinyCustomDataSet::class)
            ->and($ds->years())->toBe([2000, 2001]);
    });

    it('subclass serializes and restores as subclass', function (): void {
        $original = new TinyCustomDataSet;

        $restored = unserialize(serialize($original));

        expect($restored)->toBeInstanceOf(TinyCustomDataSet::class)
            ->and($restored->getBaseEnglishDate())->toBe($original->getBaseEnglishDate());
    });
});
