<?php

declare(strict_types=1);

use Dipesh\NepaliDate\DataSet;
use Dipesh\NepaliDate\NepaliDate;
use Dipesh\NepaliDate\Services\DateProcessor;
use Dipesh\NepaliDate\SystemDataSet;
use Tests\TinyCustomDataSet;

beforeEach(function (): void {
    $this->tinyRows = [
        2000 => [30, 30, 30, 30, 30, 30, 30, 30, 30, 30, 30, 30],
        2001 => [30, 30, 30, 30, 30, 30, 30, 30, 30, 30, 30, 30],
    ];

    $this->tinyDataSet = DataSet::make(
        $this->tinyRows,
        SystemDataSet::DEFAULT_BASE_ENGLISH_DATE,
        SystemDataSet::DEFAULT_EQUIVALENT_NEPALI_DATE,
    );
});

describe('DateProcessor with custom datasets', function (): void {
    it('uses custom dataset rows', function (): void {
        $processor = new DateProcessor($this->tinyDataSet);

        expect($processor->getDays(2000, 1, 1))->toBe(1)
            ->and($processor->getDays(2000, 2, 1))->toBe(31)
            ->and($processor->getDays(2001, 1, 1))->toBe(361);
    });

    it('falls back to packaged lookup table', function (): void {
        $processor = new DateProcessor;

        expect($processor->getDays(2000, 9, 17))->toBe(263)
            ->and($processor->getDaysFromBase(2000, 9, 17))->toBe(0)
            ->and($processor->getDaysFromBase(2000, 9, 18))->toBe(1)
            ->and($processor->getDataSet())->toBeNull();
    });

    it('exposes custom dataset rows to processor', function (): void {
        $processor = new DateProcessor($this->tinyDataSet);

        expect($processor->getDataSet())->toBeInstanceOf(DataSet::class)
            ->and(iterator_to_array($processor->getDataSet()))->toBe($this->tinyRows)
            ->and($processor->getDaysFromBase(2000, 9, 17))->toBe(0);
    });

    it('works with subclass datasets', function (): void {
        $ds = new TinyCustomDataSet;
        $processor = new DateProcessor($ds);

        expect($processor->getDataSet())->toBeInstanceOf(TinyCustomDataSet::class)
            ->and($processor->getDays(2000, 1, 1))->toBe(1)
            ->and($processor->getDaysFromBase(2000, 1, 1))->toBe(0);
    });
});

describe('NepaliDate with custom datasets', function (): void {
    it('converts using custom dataset', function (): void {
        $nepali = NepaliDate::make('2000/09/17', $this->tinyDataSet);

        expect($nepali->toAd()->format('Y-m-d'))->toBe('1944-01-01');
    });

    it('converts from AD using custom dataset', function (): void {
        $nepali = NepaliDate::fromADDate('1944/01/01', $this->tinyDataSet);

        expect($nepali->format('Y/m/d'))->toBe('2000/09/17');
    });

    it('round-trips BS → AD → BS', function (): void {
        $nepali = NepaliDate::make('2001/03/15', $this->tinyDataSet);
        $ad = $nepali->toAd();
        $back = NepaliDate::fromADDate($ad->format('Y/m/d'), $this->tinyDataSet);

        expect($back->format('Y/m/d'))->toBe('2001/03/15');
    });

    it('works with subclass datasets', function (): void {
        $ds = new TinyCustomDataSet;

        $nepali = NepaliDate::make('2000/02/01', $ds);
        expect($nepali->toAd()->format('Y-m-d'))->toBe('2000-01-31');

        $back = NepaliDate::fromADDate('2000/01/31', $ds);
        expect($back->format('Y/m/d'))->toBe('2000/02/01');
    });

    it('respects custom base dates', function (): void {
        $ds = DataSet::make($this->tinyRows, '2000/01/01', '2000/01/01');

        $nepali = NepaliDate::make('2000/01/01', $ds);
        expect($nepali->toAd()->format('Y-m-d'))->toBe('2000-01-01');

        $nepali = NepaliDate::make('2000/02/01', $ds);
        expect($nepali->toAd()->format('Y-m-d'))->toBe('2000-01-31');
    });
});

describe('SystemDataSet', function (): void {
    it('has system default base dates', function (): void {
        $ds = new SystemDataSet;

        expect($ds->getBaseEnglishDate())->toBe(SystemDataSet::DEFAULT_BASE_ENGLISH_DATE)
            ->and($ds->getEquivalentNepaliDate())->toBe(SystemDataSet::DEFAULT_EQUIVALENT_NEPALI_DATE)
            ->and($ds->isEmpty())->toBeTrue();
    });

    it('make() preserves system base dates', function (): void {
        $ds = SystemDataSet::make([2000 => $this->tinyRows[2000]]);

        expect($ds)->toBeInstanceOf(SystemDataSet::class)
            ->and($ds->getBaseEnglishDate())->toBe(SystemDataSet::DEFAULT_BASE_ENGLISH_DATE)
            ->and($ds->getEquivalentNepaliDate())->toBe(SystemDataSet::DEFAULT_EQUIVALENT_NEPALI_DATE);
    });

    it('packaged() covers BS 2000–2090', function (): void {
        $packaged = SystemDataSet::packaged();

        expect($packaged->firstYear())->toBe(2000)
            ->and($packaged->lastYear())->toBe(2090)
            ->and($packaged->count())->toBe(91);
    });

    it('default behaviour matches packaged dataset', function (): void {
        $packaged = SystemDataSet::packaged();

        $viaDefault = NepaliDate::make('2000/09/17');
        $viaPackaged = NepaliDate::make('2000/09/17', $packaged);

        expect($viaDefault->format('Y/m/d'))->toBe($viaPackaged->format('Y/m/d'))
            ->and($viaPackaged->toAd()->format('Y/m/d'))->toBe('1944/01/01');
    });
});

describe('Extended datasets', function (): void {
    it('supports appending years to packaged data', function (): void {
        $ds = SystemDataSet::packaged()
            ->append(2091, [31, 32, 31, 32, 31, 30, 30, 30, 29, 30, 29, 31]);

        $processor = new DateProcessor($ds);

        expect($ds->lastYear())->toBe(2091)
            ->and($processor->getDays(2091, 1, 1))->toBeGreaterThan(0);

        $nepali = NepaliDate::make('2091/01/01', $ds);
        expect($nepali->getYear())->toBe(2091);
    });
});

describe('Default behaviour', function (): void {
    it('default conversion matches expected epoch', function (): void {
        $nepali = NepaliDate::make('2000/09/17');

        expect($nepali->toAd()->format('Y/m/d'))->toBe('1944/01/01')
            ->and(NepaliDate::fromADDate('1944/01/01')->format('Y/m/d'))->toBe('2000/09/17');
    });

    it('make() without dataset matches constructor', function (): void {
        $withNull = NepaliDate::make('2081-4-25');
        $legacy = new NepaliDate('2081-4-25');

        expect($withNull->format('Y/m/d'))->toBe($legacy->format('Y/m/d'));
    });
});
