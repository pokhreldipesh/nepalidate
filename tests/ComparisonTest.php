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

describe('Anniversary', function (): void {
    it('is an anniversary for the same month and day in a different year', function (): void {
        expect(NepaliDate::make('2082/04/25')->isAnniversary('2081/04/25'))->toBeTrue();
    });

    it('is an anniversary for the same date', function (): void {
        expect(NepaliDate::make('2081/04/25')->isAnniversary('2081/04/25'))->toBeTrue();
    });

    it('is not an anniversary when the month differs', function (): void {
        expect(NepaliDate::make('2082/05/25')->isAnniversary('2081/04/25'))->toBeFalse();
    });

    it('is not an anniversary when the day differs', function (): void {
        expect(NepaliDate::make('2082/04/26')->isAnniversary('2081/04/25'))->toBeFalse();
    });

    it('accepts a Date object as the reference date', function (): void {
        expect(NepaliDate::make('2082/04/25')->isAnniversary(NepaliDate::make('2081/04/25')))->toBeTrue();
    });

    it('accepts flexible separators in the reference date string', function (): void {
        expect(NepaliDate::make('2082/04/25')->isAnniversary('2081-04-25'))->toBeTrue();
    });

    it('defaults to today when no reference date is given', function (): void {
        expect(NepaliDate::now()->isAnniversary())->toBeTrue();
    });

    it('treats an explicit null the same as omitting the reference date', function (): void {
        expect(NepaliDate::now()->isAnniversary(null))->toBeTrue();
    });

    it('is not an anniversary of today for a different day', function (): void {
        expect(NepaliDate::now()->addDays(1)->isAnniversary())->toBeFalse();
    });
});

describe('Birthday', function (): void {
    it('mirrors isAnniversary for a matching month and day', function (): void {
        expect(NepaliDate::make('2082/04/25')->isBirthday('2081/04/25'))->toBeTrue();
    });

    it('mirrors isAnniversary for a non-matching date', function (): void {
        expect(NepaliDate::make('2082/04/25')->isBirthday('2081/05/25'))->toBeFalse();
    });

    it('defaults to today when no reference date is given', function (): void {
        expect(NepaliDate::now()->isBirthday())->toBeTrue();
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
