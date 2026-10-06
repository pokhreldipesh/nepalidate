<?php

declare(strict_types=1);

use Dipesh\NepaliDate\NepaliDate;

describe('NepaliDate formatting', function (): void {
    it('formats in English', function (): void {
        expect(NepaliDate::make('2081-4-25')->format('Y, m, M, F, d, w, D, l, g'))
            ->toBe('2081, 04, , Shrawan, 25, 6, Sukra, Sukrabar, Gate');
    });

    it('formats in Nepali', function (): void {
        expect(NepaliDate::make('2081-4-25')->setLang('np')->format('Y, m, M, F, d, w, D, l, g'))
            ->toBe('२०८१, ०४, , साउन, २५, ६, शुक्र, शुक्रबार, गते');
    });
});

describe('NepaliDate factories', function (): void {
    it('creates from make()', function (): void {
        $date = NepaliDate::make('2081/04/25');

        expect($date)->toBeInstanceOf(NepaliDate::class)
            ->and($date->getDate())->toBe('2081/04/25');
    });

    it('creates from create()', function (): void {
        $date = NepaliDate::make('2081/04/25');
        $new = $date->create('2082/01/01');

        expect($new->getDate())->toBe('2082/01/01')
            ->and($date->getDate())->toBe('2081/04/25');
    });

    it('casts to string', function (): void {
        expect((string) NepaliDate::make('2081/04/25'))->toBe('2081/04/25');
    });
});

describe('NepaliDate mutable setUp', function (): void {
    it('re-parses in place', function (): void {
        $date = NepaliDate::make('2081/04/25');
        $date->setUp('2082/01/01');

        expect($date->getDate())->toBe('2082/01/01')
            ->and($date->getYear())->toBe(2082)
            ->and($date->getMonth())->toBe(1)
            ->and($date->getDay())->toBe(1);
    });

    it('recomputes weekday after setUp', function (): void {
        $date = NepaliDate::make('2081/04/25');
        $originalWeekDay = $date->getWeekDay();

        $date->setUp('2081/04/26');

        expect($date->getWeekDay())->not->toBe($originalWeekDay);
    });
});

describe('NepaliDate weekday', function (): void {
    it('computes weekday on construction', function (): void {
        $date = NepaliDate::make('2081/04/25');

        expect($date->getWeekDay())->toBe(6);
    });

    it('weekDay() formats as number', function (): void {
        expect(NepaliDate::make('2081/04/25')->weekDay('w'))->toBe('6');
    });
});
