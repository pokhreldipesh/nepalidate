<?php

declare(strict_types=1);

use Dipesh\NepaliDate\Contracts\Date as DateContract;
use Dipesh\NepaliDate\lang\English;
use Dipesh\NepaliDate\lang\Nepali;
use Dipesh\NepaliDate\Services\Date;

describe('Construction & parsing', function (): void {
    it('constructs with standard format', function (): void {
        $date = new Date('2078/01/01', new English);

        expect($date->getDate())->toBe('2078/01/01')
            ->and($date->getYear())->toBe(2078)
            ->and($date->getMonth())->toBe(1)
            ->and($date->getDay())->toBe(1);
    });

    it('zero-pads components', function (): void {
        $date = new Date('2078/1/2', new English);

        expect($date->getDate())->toBe('2078/01/02')
            ->and($date->getMonth())->toBe(1)
            ->and($date->getDay())->toBe(2);
    });

    it('accepts alternate separators', function (string $input): void {
        expect((new Date($input, new English))->getDate())->toBe('2078/01/01');
    })->with(['2078/01/01', '2078-01-01', '2078.1.1']);

    it('rejects invalid dates', function (string $input): void {
        new Date($input, new English);
    })->with([
        'empty' => [''],
        'year only' => ['2078'],
        'year month' => ['2078/01'],
        'four components' => ['2078/01/01/02'],
        'non numeric' => ['abc'],
        'month 13' => ['2078/13/01'],
        'month 0' => ['2078/00/01'],
    ])->throws(Exception::class, "Invalid date format. Please use 'YYYY/MM/DD'.");
});

describe('Value-object purity', function (): void {
    it('has no dataset property', function (): void {
        expect(property_exists(Date::class, 'dataSet'))->toBeFalse();
    });

    it('has no date processor property', function (): void {
        expect(property_exists(Date::class, 'dateProcessor'))->toBeFalse();
    });

    it('does not have processing methods', function (): void {
        expect(method_exists(Date::class, 'getTotalDaysFromBaseDate'))->toBeFalse()
            ->and(method_exists(Date::class, 'diffDays'))->toBeFalse()
            ->and(method_exists(Date::class, 'addDays'))->toBeFalse()
            ->and(method_exists(Date::class, 'toAd'))->toBeFalse()
            ->and(method_exists(Date::class, 'getDateProcessor'))->toBeFalse();
    });

    it('implements the date contract', function (): void {
        expect(new Date('2078/01/01', new English))->toBeInstanceOf(DateContract::class);
    });

    it('has a weekday property', function (): void {
        $date = new Date('2078/01/01', new English);

        expect(property_exists(Date::class, 'weekDay'))->toBeTrue()
            ->and($date->getWeekDay())->toBeInt();
    });
});

describe('Language-aware accessors', function (): void {
    it('returns English digits', function (): void {
        $date = new Date('2078/01/15', new English);

        expect($date->day())->toBe('15')
            ->and($date->year())->toBe('2078');
    });

    it('returns Devanagari digits for Nepali', function (): void {
        $date = new Date('2078/01/15', new Nepali);

        expect($date->day())->toBe('१५')
            ->and($date->year())->toBe('२०७८');
    });

    it('formats month as zero-padded number', function (): void {
        expect((new Date('2078/04/01', new English))->month('m'))->toBe('04');
    });

    it('formats month as full name', function (): void {
        expect((new Date('2078/04/01', new English))->month('F'))->toBe('Shrawan');
    });

    it('throws on unsupported month format', function (): void {
        (new Date('2078/01/01', new English))->month('x');
    })->throws(Exception::class);

    it('resolves language from string codes', function (string $code, string $expected): void {
        $date = new Date('2078/01/01', new English);

        expect($date->resolveLanguage($code))->toBeInstanceOf($expected);
    })->with([
        ['np', Nepali::class],
        ['en', English::class],
    ]);

    it('returns same instance for language objects', function (): void {
        $date = new Date('2078/01/01', new English);
        $lang = new Nepali;

        expect($date->resolveLanguage($lang))->toBe($lang);
    });

    it('throws for unsupported language', function (): void {
        (new Date('2078/01/01', new English))->resolveLanguage('xx');
    })->throws(Exception::class, 'The specified language type is not supported.');
});

describe('Formatting', function (): void {
    it('formats with default Y/m/d', function (): void {
        expect((new Date('2078/01/01', new English))->format())->toBe('2078/01/01');
    });

    it('keeps literal separators', function (): void {
        expect((new Date('2078/01/01', new English))->format('Y-m-d'))->toBe('2078-01-01');
    });

    it('formats with language override', function (): void {
        $date = new Date('2078/01/15', new English);

        expect($date->format('Y/m/d', 'np'))->toBe('२०७८/०१/१५');
    });

    it('does not persist language override', function (): void {
        $date = new Date('2078/01/15', new English);
        $date->format('Y/m/d', 'np');

        expect($date->getLanguage())->toBeInstanceOf(English::class);
    });

    it('throws on unsupported format character', function (): void {
        (new Date('2078/01/01', new English))->format('Y/m/d/x');
    })->throws(Exception::class, 'Invalid date format');
});

describe('withDate (immutable factory)', function (): void {
    it('creates a new instance with a different date', function (): void {
        $date = new Date('2078/01/01', new English);
        $new = $date->withDate('2079/02/03');

        expect($new)->not->toBe($date)
            ->and($new->getYear())->toBe(2079)
            ->and($new->getMonth())->toBe(2)
            ->and($new->getDay())->toBe(3)
            ->and($new->getDate())->toBe('2079/02/03');
    });

    it('preserves the original instance', function (): void {
        $date = new Date('2078/01/01', new English);
        $date->withDate('2079/02/03');

        expect($date->getDate())->toBe('2078/01/01');
    });

    it('preserves the language', function (): void {
        $date = new Date('2078/01/01', new Nepali);
        $new = $date->withDate('2079/02/03');

        expect($new->getLanguage())->toBeInstanceOf(Nepali::class);
    });
});

describe('parseComponents (static)', function (): void {
    it('parses into integer components', function (): void {
        expect(Date::parseComponents('2078/01/15'))->toBe([2078, 1, 15]);
    });

    it('accepts alternate separators', function (string $input, array $expected): void {
        expect(Date::parseComponents($input))->toBe($expected);
    })->with([
        ['2078-01-01', [2078, 1, 1]],
        ['2078.12.31', [2078, 12, 31]],
    ]);

    it('rejects invalid strings', function (string $input): void {
        Date::parseComponents($input);
    })->with([
        'empty' => [''],
        'non numeric' => ['abc'],
    ])->throws(Exception::class);
});
