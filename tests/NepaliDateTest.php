<?php

declare(strict_types=1);

use Dipesh\Date\Date as SystemDate;
use Dipesh\Date\lang\English;
use Dipesh\NepaliDate\NepaliDate;

describe('NepaliDate wiring', function (): void {
    it('composes the system-level Date from dipesh/date', function (): void {
        expect(NepaliDate::make('2081/04/25')->date)->toBeInstanceOf(SystemDate::class);
    });

    it('exposes the normalized string through the composed Date', function (): void {
        expect(NepaliDate::make('2081/04/25')->date->getDate())->toBe('2081/04/25');
    });

    it('forwards date method calls to the composed Date', function (): void {
        expect(NepaliDate::make('2081/04/25')->getDate())->toBe('2081/04/25')
            ->and(NepaliDate::make('2081/04/25')->getYear())->toBe(2081)
            ->and(NepaliDate::make('2081/04/25')->format('Y/m/d'))->toBe('2081/04/25');
    });

    it('forwards date property reads to the composed Date', function (): void {
        $date = NepaliDate::make('2081/04/25');

        expect($date->year)->toBe(2081)
            ->and($date->month)->toBe(4)
            ->and($date->day)->toBe(25)
            ->and($date->weekDay)->toBe(6);
    });

    it('keeps the weekday on the composed Date so formatting is correct', function (): void {
        expect(NepaliDate::make('2081/04/25')->date->getWeekDay())->toBe(6);
    });

    it('unwraps NepaliDate arguments when delegating comparisons', function (): void {
        expect(NepaliDate::make('2081/04/25')->isEqual(NepaliDate::make('2081/04/25')))->toBeTrue()
            ->and(NepaliDate::make('2081/04/25')->isGreaterThan(NepaliDate::make('2081/04/24')))->toBeTrue();
    });

    it('returns a NepaliDate from formatUsing so chains keep calendar methods', function (): void {
        $date = NepaliDate::make('2081/04/25')->formatUsing(fn (string $format, $d): string => '<'.$format.'>');

        expect($date)->toBeInstanceOf(NepaliDate::class)
            ->and($date->format('Y/m/d'))->toBe('<Y/m/d>')
            ->and($date->addDays(3)->getDate())->toBe('2081/04/28');
    });

    it('returns a NepaliDate from languageUsing so chains keep calendar methods', function (): void {
        $date = NepaliDate::make('2081/04/25')->languageUsing(new English);

        expect($date)->toBeInstanceOf(NepaliDate::class)
            ->and($date->addDays(1)->getDate())->toBe('2081/04/26');
    });

    it('leaves the original untouched when styling a copy', function (): void {
        $date = NepaliDate::make('2081/04/25');
        $date->formatUsing(fn (string $format, $d): string => 'x');

        expect($date->format('Y/m/d'))->toBe('2081/04/25');
    });
});

describe('Language-aware accessors', function (): void {
    it('formats raw components in English', function (): void {
        $date = NepaliDate::make('2081/04/25');

        expect($date->year())->toBe('2081')
            ->and($date->day())->toBe('25');
    });

    it('formats components in Nepali digits', function (): void {
        $date = NepaliDate::make('2081/04/25')->setLang('np');

        expect($date->year())->toBe('२०८१')
            ->and($date->day())->toBe('२५');
    });

    it('formats month as a zero-padded number', function (): void {
        expect(NepaliDate::make('2081/04/25')->month('m'))->toBe('04');
    });

    it('formats month as a full name', function (): void {
        expect(NepaliDate::make('2081/04/25')->month('F'))->toBe('Shrawan');
    });

    it('throws on an unsupported month format', function (): void {
        NepaliDate::make('2081/04/25')->month('x');
    })->throws(Exception::class, 'Unsupported month format');
});

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
