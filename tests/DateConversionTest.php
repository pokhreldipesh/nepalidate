<?php

declare(strict_types=1);

use Dipesh\NepaliDate\EnDate;
use Dipesh\NepaliDate\NepaliDate;

describe('AD ↔ BS conversion', function (): void {
    it('converts BS to AD (today round-trip)', function (): void {
        $today = (new EnDate)->format('Y-m-d');

        expect(NepaliDate::make(NepaliDate::fromADDate($today)->getDate())->toAd()->format('Y-m-d'))
            ->toBe($today);
    });

    it('converts AD to BS', function (): void {
        $nepali = NepaliDate::fromADDate('1944/01/01');

        expect($nepali->getDate())->toBe('2000/09/17');
    });

    it('converts specific AD dates to BS', function (string $ad, string $bs): void {
        expect(NepaliDate::fromADDate($ad)->getDate())->toBe($bs);
    })->with([
        ['1944/01/01', '2000/09/17'],
        ['2000/01/01', '2056/09/17'],
        ['2023/01/01', '2079/09/17'],
    ]);

    it('round-trips BS → AD → BS', function (): void {
        $bs = '2081/04/25';
        $ad = NepaliDate::make($bs)->toAd()->format('Y/m/d');

        expect(NepaliDate::fromADDate($ad)->getDate())->toBe($bs);
    });
});
