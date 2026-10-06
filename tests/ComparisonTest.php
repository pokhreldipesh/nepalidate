<?php

declare(strict_types=1);

use Dipesh\NepaliDate\NepaliDate;

describe('Date manipulation', function (): void {
    it('adds days', function (): void {
        expect(NepaliDate::make('2081/04/24')->addDays(4)->getDate())->toBe('2081/04/28');
    });

    it('subtracts days', function (): void {
        expect(NepaliDate::make('2081/04/24')->subDays(4)->getDate())->toBe('2081/04/20');
    });

    it('addDays returns a new instance', function (): void {
        $date = NepaliDate::make('2081/04/24');
        $new = $date->addDays(4);

        expect($new)->not->toBe($date)
            ->and($date->getDate())->toBe('2081/04/24');
    });
});

describe('Date comparison', function (): void {
    it('checks equality', function (): void {
        expect(NepaliDate::make('2081/04/24')->isEqual('2081/04/24'))->toBeTrue();
    });

    it('checks inequality', function (): void {
        expect(NepaliDate::make('2081/04/24')->isEqual('2081/04/25'))->toBeFalse();
    });

    it('checks greater than', function (): void {
        expect(NepaliDate::make('2081/04/25')->isGreaterThan('2081/04/24'))->toBeTrue();
    });

    it('checks less than', function (): void {
        expect(NepaliDate::make('2081/04/24')->isLessThan('2081/04/25'))->toBeTrue();
    });
});

describe('Day difference', function (): void {
    it('computes positive difference', function (): void {
        expect(NepaliDate::make('2081/04/24')->diffDays('2081/04/28'))->toBe(4);
    });

    it('computes negative difference', function (): void {
        expect(NepaliDate::make('2081/04/28')->diffDays('2081/04/24'))->toBe(-4);
    });

    it('computes zero difference', function (): void {
        expect(NepaliDate::make('2081/04/24')->diffDays('2081/04/24'))->toBe(0);
    });
});
