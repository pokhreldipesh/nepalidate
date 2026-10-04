<?php

namespace Tests;

use Dipesh\NepaliDate\Contracts\Date as DateContract;
use Dipesh\NepaliDate\lang\English;
use Dipesh\NepaliDate\lang\Nepali;
use Dipesh\NepaliDate\Services\Date;
use Dipesh\NepaliDate\Services\FormatDate;
use PHPUnit\Framework\TestCase;

class DateTest extends TestCase
{
    // ── Construction & parsing ──────────────────────────────────────

    public function test_constructs_with_standard_format(): void
    {
        $date = new Date('2078/01/01', new English);

        $this->assertSame('2078/01/01', $date->date);
        $this->assertSame(2078, $date->year);
        $this->assertSame(1, $date->month);
        $this->assertSame(1, $date->day);
    }

    public function test_zero_pads_components(): void
    {
        $date = new Date('2078/1/2', new English);

        $this->assertSame('2078/01/02', $date->date);
        $this->assertSame(1, $date->month);
        $this->assertSame(2, $date->day);
    }

    public function test_accepts_alternate_separators(): void
    {
        $this->assertSame('2078/01/01', (new Date('2078-01-01', new English))->date);
        $this->assertSame('2078/01/01', (new Date('2078.1.1', new English))->date);
    }

    /**
     * @dataProvider invalidDateProvider
     */
    public function test_rejects_invalid_dates(string $input): void
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage("Invalid date format. Please use 'YYYY/MM/DD'.");

        new Date($input, new English);
    }

    public static function invalidDateProvider(): array
    {
        return [
            'empty' => [''],
            'year only' => ['2078'],
            'year month' => ['2078/01'],
            'four components' => ['2078/01/01/02'],
            'non numeric' => ['abc'],
            'month 13' => ['2078/13/01'],
            'month 0' => ['2078/00/01'],
        ];
    }

    public function test_set_up_reparses_in_place(): void
    {
        $date = new Date('2078/01/01', new English);
        $date->setUp('2079/02/03');

        $this->assertSame(2079, $date->year);
        $this->assertSame(2, $date->month);
        $this->assertSame(3, $date->day);
        $this->assertSame('2079/02/03', $date->date);
    }

    // ── Value-object purity ─────────────────────────────────────────

    public function test_has_no_dataset_property(): void
    {
        $this->assertFalse(property_exists(Date::class, 'dataSet'));
    }

    public function test_has_no_date_processor_property(): void
    {
        $this->assertFalse(property_exists(Date::class, 'dateProcessor'));
    }

    public function test_does_not_have_processing_methods(): void
    {
        $this->assertFalse(method_exists(Date::class, 'getTotalDaysFromBaseDate'));
        $this->assertFalse(method_exists(Date::class, 'diffDays'));
        $this->assertFalse(method_exists(Date::class, 'addDays'));
        $this->assertFalse(method_exists(Date::class, 'toAd'));
        $this->assertFalse(method_exists(Date::class, 'getDateProcessor'));
    }

    public function test_implements_date_contract(): void
    {
        $this->assertInstanceOf(DateContract::class, new Date('2078/01/01', new English));
    }

    public function test_has_weekday_property(): void
    {
        $date = new Date('2078/01/01', new English);

        $this->assertTrue(property_exists(Date::class, 'weekDay'));
        $this->assertIsInt($date->weekDay);
    }

    // ── Language-aware accessors ────────────────────────────────────

    public function test_day_returns_english_digits(): void
    {
        $date = new Date('2078/01/15', new English);

        $this->assertSame('15', $date->day());
    }

    public function test_year_returns_english_digits(): void
    {
        $date = new Date('2078/01/01', new English);

        $this->assertSame('2078', $date->year());
    }

    public function test_year_returns_devanagari_digits_for_nepali(): void
    {
        $date = new Date('2078/01/01', new Nepali);

        $this->assertSame('२०७८', $date->year());
    }

    public function test_day_returns_devanagari_digits_for_nepali(): void
    {
        $date = new Date('2078/01/15', new Nepali);

        $this->assertSame('१५', $date->day());
    }

    public function test_month_numeric_format(): void
    {
        $date = new Date('2078/04/01', new English);

        $this->assertSame('04', $date->month('m'));
    }

    public function test_month_short_name(): void
    {
        $date = new Date('2078/04/01', new English);

        // English short names ('M') are intentionally empty; only full names are populated.
        $this->assertSame('', $date->month('M'));
    }

    public function test_month_full_name(): void
    {
        $date = new Date('2078/04/01', new English);

        $this->assertSame('Shrawan', $date->month('F'));
    }

    public function test_month_unsupported_format_throws(): void
    {
        $this->expectException(\Exception::class);

        (new Date('2078/01/01', new English))->month('x');
    }

    public function test_resolve_language_from_string(): void
    {
        $date = new Date('2078/01/01', new English);

        $this->assertInstanceOf(Nepali::class, $date->resolveLanguage('np'));
        $this->assertInstanceOf(English::class, $date->resolveLanguage('en'));
    }

    public function test_resolve_language_from_instance(): void
    {
        $date = new Date('2078/01/01', new English);
        $lang = new Nepali;

        $this->assertSame($lang, $date->resolveLanguage($lang));
    }

    public function test_resolve_language_unsupported_throws(): void
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('The specified language type is not supported.');

        (new Date('2078/01/01', new English))->resolveLanguage('xx');
    }

    // ── Formatting ──────────────────────────────────────────────────

    public function test_format_default(): void
    {
        $date = new Date('2078/01/01', new English);

        $this->assertSame('2078/01/01', $date->format());
    }

    public function test_format_custom_separators(): void
    {
        $date = new Date('2078/01/01', new English);

        $this->assertSame('2078-01-01', $date->format('Y-m-d'));
    }

    public function test_format_with_language_override(): void
    {
        $date = new Date('2078/01/15', new English);

        $this->assertSame('२०७८/०१/१५', $date->format('Y/m/d', 'np'));
    }

    public function test_format_unsupported_character_throws(): void
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Invalid date format');

        (new Date('2078/01/01', new English))->format('Y/m/d/x');
    }

    public function test_get_formatter_returns_format_date(): void
    {
        $date = new Date('2078/01/01', new English);

        $this->assertInstanceOf(FormatDate::class, $date->getFormatter());
    }

    // ── parseComponents (static) ────────────────────────────────────

    public function test_parse_components_returns_ints(): void
    {
        $result = Date::parseComponents('2078/01/15');

        $this->assertSame([2078, 1, 15], $result);
    }

    public function test_parse_components_accepts_alternate_separators(): void
    {
        $this->assertSame([2078, 1, 1], Date::parseComponents('2078-01-01'));
        $this->assertSame([2078, 12, 31], Date::parseComponents('2078.12.31'));
    }

    /**
     * @dataProvider invalidDateProvider
     */
    public function test_parse_components_rejects_invalid(string $input): void
    {
        $this->expectException(\Exception::class);

        Date::parseComponents($input);
    }
}
