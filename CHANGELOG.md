# Changelog

All notable changes on this branch (`v3.0`) are documented here.

## [Unreleased] — Tooling: one docblock style across Pint and Rector

### Changed

- **`pint.json`** — the `fully_qualified_strict_types` disable is **removed**, so Pint's default applies and FQCNs in docblocks get shortened to imported short names. `pint.json` is now an empty `{"rules": {}}` placeholder. This supersedes the earlier decision to keep FQCNs (added with the `dipesh/date` extraction).
- **`rector.php`** — `withImportNames(importDocBlockNames: true)` to match, so Rector shortens the same docblock types and adds the `use` imports they resolve against. The two tools now converge instead of reverting each other: Rector shortens and imports, Pint then normalizes import order.
- **`src/NepaliDate.php`** — `@method` / `@property-read` / `@param` / `@throws` docblock tags normalized to short names, with `use Exception;` added for the `@throws` tags.
- **`rector.php`** — `RemoveNullArgOnNullDefaultParamRector` skipped for `tests/`. Those cases deliberately pass an explicit `null` to assert it behaves the same as omitting the argument, so the rule's rewrite would collapse the call and delete the behaviour under test. `vendor/bin/rector --dry-run` reports no changes.

## [3.0.1] — Packaged calendar data correction (BS 2081–2083)

### Added

- **`composer.json`** — explicit `"version": "3.0.1"` field. Note this must be bumped by hand on every release, since Composer would otherwise infer it from the git tag.
- **`README.md`** — dynamic Packagist version badge (`img.shields.io/packagist/v/dipesh/nepali-date`), which tracks the latest published version automatically.

### Fixed

- **`SystemDataSet::packaged()` month lengths for BS 2081, 2082 and 2083 were wrong**, so every AD ↔ BS conversion landing inside those three BS years was off by up to a day. Reported symptom: `NepaliDate::now()->format('Y-m-d')` returned `2083-07-23` for 2026-11-08, but the correct BS date is `2083-07-22`.
  - BS 2081: `[31, 31, 32, 32, 31, 30, 30, 30, 29, 30, 30, 30]` → `[31, 32, 31, 32, 31, 30, 30, 30, 29, 30, 29, 31]`
  - BS 2082: `[30, 32, 31, 32, 31, 30, 30, 30, 29, 30, 30, 30]` → `[31, 31, 32, 31, 31, 31, 30, 29, 30, 29, 30, 30]`
  - BS 2083: `[31, 31, 32, 31, 31, 30, 30, 30, 29, 30, 30, 30]` → `[31, 31, 32, 31, 31, 31, 30, 29, 30, 29, 30, 30]`
  - **Year totals are unchanged** (366 / 365 / 365), so no year boundary moved: `2000/09/17` ↔ `1944/01/01` and all round-trips behave exactly as before. Only the intra-year month distribution was wrong.
  - Root cause: the 2081–2083 block was a shifted copy of stale pre-correction data (it matched `bikram-sambat` v1.7.0's 2079–2081 rows), while the rest of the table was already correct.
- **`tests/DateConversionTest.php`** — four AD ↔ BS pairs added to pin the corrected rows, including the reported symptom `2026-11-08 → 2083/07/22` and the Ashoj-31 boundary `2026-10-17 → 2083/06/31`.

## [Unreleased] — `dipesh/date` extraction (local date layer removed)

### Added

- **`composer.json`** — new runtime dependency **`dipesh/date: ^1.0`** from Packagist (published v1.0.0, `github.com/pokhreldipesh/date`). This is the system-level BS date value object library extracted from this package. The earlier Composer path repository (`../date`, symlinked) has been removed — `vendor/dipesh/date` is now a real install from the GitHub archive.
- **`Date::withWeekDay(int $weekDay): static`** in `dipesh/date` — see "Design note" below.
- **`pint.json`** — disabled `fully_qualified_strict_types` so FQCNs in docblocks would survive formatting (this disable was later removed — see "Tooling: one docblock style across Pint and Rector" above).

### Changed

- **`NepaliDate` now *composes* a `Dipesh\Date\Date`** (exposed as `$date`) instead of extending a locally-defined `Services\Date`. See "Design note" below.
- **`$nepali->date` is the composed `Date` object, not the normalized string.** Use `$nepali->date->getDate()` or `$nepali->getDate()`.
- Date-level methods and properties are **forwarded** from `NepaliDate` to the composed `Date` via `__call()` / `__get()` / `__callStatic()`, so `$nepali->format(...)`, `$nepali->getYear()`, `$nepali->year`, `NepaliDate::parseComponents(...)` etc. keep working. The authoritative list is the `@method` / `@property-read` block on `NepaliDate`.
- **`NepaliDate::__construct`** language parameter widened: `?Language $language` → `LanguagePack|Language|null $language`.
- **`NepaliDate::setLang()`** widened: `string|Language` → `string|LanguagePack|Language`.
- **`formatUsing()` / `languageUsing()`** return `static` (not the composed `Date`) so fluent chains keep calendar methods.
- **`year()` / `month()` / `day()`** (language-aware component accessors) stay on `NepaliDate`; `dipesh/date`'s `Date` does not provide them.
- **`isEqual()` / `isGreaterThan()` / `isLessThan()`** are delegated to `Dipesh\Date\Concerns\HasComparisonDate` — **component-wise** ordering (year, then month, then day) rather than day-math through `DateProcessor`. Equivalent results for dates inside the calendar range, and they no longer throw for dates outside it. `NepaliDate` arguments are unwrapped to their composed `Date` before delegation.
- **`isAnniversary()` / `isBirthday()`** remain on `NepaliDate` (inlined — the `Concerns\HasDateComparison` trait is gone). `dipesh/date` has no `isBirthday`, and its `isAnniversary()` requires an explicit argument because that package has no clock; ours keeps the documented `null` → today default.
- `README.md` — architecture diagram, design-patterns table, and extension examples re-pointed at `dipesh/date` types. The "Custom Language" example now implements `LanguagePack` (data-only `toArray()`).

### Removed

Everything that duplicated the extracted date layer:

- **`src/Services/Date.php`** → `Dipesh\Date\Date`
- **`src/Services/Formatter.php`** → `Dipesh\Date\Formatter`
- **`src/Services/FormatDate.php`** → `Dipesh\Date\FormatDate`
- **`src/Contracts/Date.php`** → `Dipesh\Date\Contracts\Date`
- **`src/Contracts/Formatter.php`** → `Dipesh\Date\Contracts\Formatter`
- **`src/Contracts/Language.php`** → `Dipesh\Date\Contracts\Language` + `LanguagePack`
- **`src/lang/English.php`**, **`src/lang/Nepali.php`** → `Dipesh\Date\lang\English`, `Dipesh\Date\lang\Nepali`
- **`src/Concerns/HasDateComparison.php`** — ordering ops come from `Dipesh\Date\Concerns\HasComparisonDate`; `isAnniversary()` / `isBirthday()` are inlined on `NepaliDate`.
- **`tests/DateTest.php`** — 41 tests covering the extracted `Date`; that coverage now lives in `dipesh/date`'s own suite. The language-aware accessor cases were re-added to `tests/NepaliDateTest.php`.

### Migration notes

- **`$nepali->date` changed meaning.** It used to be the normalized string (`"2081/04/25"`); it is now the composed `Dipesh\Date\Date` object. Replace `$nepali->date` with `$nepali->date->getDate()` or `$nepali->getDate()`.
- `NepaliDate` no longer passes an `instanceof Dipesh\Date\Date` check (it composes one). Type hints that relied on that must accept `NepaliDate` explicitly.
- Replace `use Dipesh\NepaliDate\Services\Date` → `use Dipesh\Date\Date` (now the composed value object).
- Replace `use Dipesh\NepaliDate\Services\{Formatter,FormatDate}` → `use Dipesh\Date\{Formatter,FormatDate}`.
- Replace `use Dipesh\NepaliDate\Contracts\{Date,Formatter,Language}` → `use Dipesh\Date\Contracts\{Date,Formatter,Language,LanguagePack}`.
- Replace `use Dipesh\NepaliDate\lang\{English,Nepali}` → `use Dipesh\Date\lang\{English,Nepali}`. Note these are now **`LanguagePack`** (data-only), not lookup classes — `new Date('…', new English)` still works because `Date` accepts a pack or a bridge.
- Custom language classes: implement `Dipesh\Date\Contracts\LanguagePack` with `toArray()` instead of implementing `Contracts\Language` with four lookup methods. See the README "Custom Language" section.
- Comparison methods now accept `NepaliDate|Date|string` and order component-wise (no calendar arithmetic).

### Design note — why composition

`NepaliDate` *has-a* `Dipesh\Date\Date` (the `$date` property) rather than extending it. The date
value object is a replaceable dependency and `NepaliDate` is a calendar-aware facade around it.
Date-level methods and properties are forwarded from the facade via `__call()` / `__get()` /
`__callStatic()`, and the full forwarded surface is declared in `NepaliDate`'s docblock
(`@method` / `@property-read`) so IDEs and PHPStan still see it.

Two consequences worth calling out:

- **`$nepali->date` is now the `Date` object, not the normalized string.** Use
  `$nepali->date->getDate()` or `$nepali->getDate()` for the string.
- **`Date::withWeekDay()` had to be added to `dipesh/date`.** `Date::$weekDay` is `protected(set)`,
  so a composing class cannot write it — only subclasses could. Without it weekday formatting
  would render `0`. It joins `withDate()` / `formatUsing()` / `languageUsing()` as part of the
  immutable `with*` family.

### Verification

- `dipesh/date` v1.0.0 (Packagist): 73 tests, 118 assertions; Pint clean; PHPStan level 8 clean.
- `dipesh/nepali-date`: 90 tests, 154 assertions; Pint clean; PHPStan level 8 clean.

## [Unreleased] — `isAnniversary` API

### Added

- **`NepaliDate::isAnniversary(Date|string|null $date = null): bool`** (in `HasDateComparison`) — returns true when the given date shares the same month and day as the current date, regardless of year (a date is its own anniversary). Accepts a `Date` object or a date string; when omitted (or `null`) the reference date defaults to today via `NepaliDate::now()` in the current calendar. Components are compared directly (no calendar arithmetic), so BS month lengths do not matter. Documented in `README.md`; covered in `tests/ComparisonTest.php`.
- **`NepaliDate::isBirthday(Date|string|null $date = null): bool`** (in `HasDateComparison`) — convenience alias for `isAnniversary()`; same signature and behaviour. Documented in `README.md`; covered in `tests/ComparisonTest.php`.

## [Unreleased] — PHP 8.4+

### Changed

- **PHP requirement raised to `>=8.4`** (was `>=8.0`).
- **Asymmetric visibility** (`public private(set)` / `public protected(set)`) on `Date` and `NepaliDate` properties — read publicly, write restricted to class hierarchy.
- **`new` without parentheses** where applicable (PHP 8.4 syntax).
- **Rector config** updated to `php84: true`.
- `DateProcessor`, `Formatter` use constructor property promotion.

## [Unreleased] — Rector + PHPStan

### Added

- **`rector.php`** — Rector config: PHP 8.0 sets, code quality, dead code, early return, type declaration rules.
- **`phpstan.neon`** — PHPStan level 8 config for `src/`.
- **`composer.json`** — new scripts: `stan`, `rector`, `check` (lint + stan + test).

### Changed

- **Rector applied to all source + test files** — `declare(strict_types=1)`, constructor property promotion, closure return types, `Stringable` interface on `NepaliDate`, `@phpstan-consistent-constructor` annotations, import cleanup.
- **PHPStan level 8 passes** — fixed `preg_replace_callback` return types, removed redundant `is_int()` (with adjusted PHPDoc), added `IteratorAggregate` generics.

## [Unreleased] — Test suite rewritten with Pest

### Changed

- **Tests** — all test files rewritten from PHPUnit classes to Pest style (`describe`/`it`/`expect`). 103 tests, 180 assertions.
  - `tests/DateTest.php` — construction, parsing, value-object purity, language accessors, formatting, `withDate`, `parseComponents`
  - `tests/NepaliDateTest.php` — formatting (EN/NP), factories, mutable `setUp`, weekday
  - `tests/ComparisonTest.php` — manipulation (`addDays`/`subDays`), comparison, `diffDays`
  - `tests/DateConversionTest.php` — AD ↔ BS conversion, round-trips
  - `tests/DataSetTest.php` — creation, mutators, validation, iteration, serialization, subclasses
  - `tests/DataSetIntegrationTest.php` — DateProcessor + NepaliDate with custom datasets, SystemDataSet
- **`composer.json`** — test script now runs Pest (`vendor/bin/pest tests`). Added `pestphp/pest` dev dependency.

## [Unreleased] — Full maintainability refactor

### Changed

- **`src/lang/English.php`**, **`src/lang/Nepali.php`** — language data moved from `public static` properties to `private`/`public` class constants. Classes are now fully immutable (no mutable state). Added `@return` array shape annotations.
- **`src/Contracts/Language.php`** — added docblocks with `@param` / `@return` array shapes for `getWeek()` and `getMonth()`.
- **`src/Services/DateProcessor.php`** — no longer depends on `Date::$defaultOutputFormat` (has own `DATE_FORMAT` constant). `getDaysFromBase` uses `self::parseYmd` on the active calendar's base date. Cleaner control flow in `getDateFromDays`.
- **`src/Services/FormatDate.php`** — `processFormatChar` uses named `const` arrays (`WEEKDAY_FORMATS`, `MONTH_FORMATS`). Explicit 'Y' fallback in `match`.
- **`src/Services/Formatter.php`** — abstract base renamed from `AbstractFormatter`. `formatNumber()` casts digit match to `int`. Cleaner `validateSupportedFormats`.
- **`src/Services/Date.php`** — `format()` with `$lang` override is temporary (uses try/finally to restore language — instance unchanged after call).
- **`src/EnDate.php`** — all arithmetic methods (`addDays`, `subDays`, `addMonths`, `subMonths`, `addYears`, `subYears`) now return **new instances** (immutable). Removed unnecessary `format()` override. `diffDays()` returns `int` (was `false|int`). Constructor `string $timezone` (was untyped).

## [Unreleased] — Formatter system refactor

### Added

- **`src/Services/Formatter.php`** — abstract base class for formatters. Takes `Date` on construct, provides shared `formatNumber()` and `validateSupportedFormats()`.

### Changed

- **`src/Contracts/Formatter.php`** — `setUp()` removed; `formatNumber` signature widened to `int|string`.
- **`src/Services/FormatDate.php`** — extends `AbstractFormatter`. No `setUp()`. Reads date state live from `$this->date`. `processFormatChar` uses `match` instead of nested `if`.
- **`src/Services/Date.php`** — `getFormatter()` passes `$this` to `FormatDate`. `assignDate()` recreates formatter. `format()` no longer calls `formatter->setUp()`. Added `__clone()` to recreate formatter on clone.
- **`src/NepaliDate.php`** — `setLang()` recreates formatter instead of `formatter->setUp()`.

## [Unreleased] — Immutable Date + withDate pattern

### Changed

- **`src/Services/Date.php`** — now immutable: `setUp()` removed. Constructor fully initializes. `withDate(string): static` returns a new instance. `assignDate()` is `protected` for subclass use.
- **`src/NepaliDate.php`** — adds `setUp()` as public mutator (re-parses + recomputes weekDay). `withDate()` overrides to preserve DataSet and recompute weekDay. `create()` uses `withDate()`.
- **`src/Concerns/HasDateManipulation.php`** — `addDays()` uses `withDate()` instead of `clone + setUp()`.
- **`src/Contracts/Date.php`** — `setUp()` removed; `withDate(string): static` added.

## [Unreleased] — Date rewrite as pure value object

### Changed

- **`src/Contracts/Date.php`** — expanded to match `Services\Date` surface:
  - Added: `getDate()`, `getYear()`, `getMonth()`, `getDay()`, `getWeekDay()`, `getLanguage()`, `format()`, `getFormatter()`, `resolveLanguage()`, `parseComponents()`.
- **`src/Services/Date.php`** — added getter methods (`getDate`, `getYear`, `getMonth`, `getDay`, `getWeekDay`, `getLanguage`) for typed interface access to raw components.
- **`src/Services/FormatDate.php`** — uses `Date` interface getters instead of direct property access.
- **`src/Concerns/HasDateOperation.php`** — uses `Date` interface getters instead of direct property access.

- **`src/Services/Date.php`** — rewritten as a pure system-level value object:
  - Removed: `$dataSet`, `$dateProcessor`, `getDateProcessor()`, `HasDateOperation` trait, `__get('weekDay')`.
  - Added: `public int $weekDay` (real property), `Date::parseComponents()` (public static).
  - Constructor simplified to `new Date(string $date, Language $language)` — no DataSet param.
  - No calendar arithmetic; delegates formatting to `Formatter`.
- **`src/NepaliDate.php`** — now owns all calculation dependencies:
  - Holds `$dataSet`, `$dateProcessor`, `getDateProcessor()`.
  - Uses `HasDateOperation` trait (moved from `Date`).
  - Overrides `setUp()` to recompute `weekDay` after parsing.
  - Constructor: `new NepaliDate(?string $date, ?Language $language, ?DataSet $dataSet)`.
- **`src/Concerns/HasDateOperation.php`** — `getTotalDaysFromBaseDate()` rewritten to use `getDays()` arithmetic (base = `getDays(target) - getDays(baseDate)`) instead of `DateProcessor::getDaysFromBase()`.
- **`src/Contracts/DateProcessor.php`** — **removed** `getDaysFromBase()` from the interface.
- **`src/Services/DateProcessor.php`** — `getDaysFromBase()` kept as `@internal` public method (used by existing tests).
- **`src/Contracts/Date.php`** — `month()` signature aligned: `month(string $format = 'm'): int|string`.

### Added

- **`tests/DateTest.php`** — 41 PHPUnit tests covering `Services\Date`: construction, parsing, validation, language-aware accessors, formatting, `parseComponents`, value-object purity.

### Migration notes

- `Date` no longer accepts `?DataSet` in its constructor; pass it to `NepaliDate` instead.
- `DateProcessor` implementations no longer need `getDaysFromBase()`.
- `$date->weekDay` is a real property (not magic `__get`); returns 0 on plain `Date`, computed on `NepaliDate`.
- `Date::parseComponents()` replaces the private `validateDateAndGetComponents()`.

### Verification

- `vendor/bin/phpunit tests` — 100 tests, 181 assertions, green.

## [Unreleased] — v3.0 calendar DataSet refactor

### Added

- **`src/DataSet.php`** — generic, iterable (`IteratorAggregate`), serializable (`Serializable`) container for calendar lookup data:
  - Year rows (`year => 12 month day-counts`), plus base English (AD) date and equivalent Nepali (BS) date.
  - `make(array $rows = [], ?string $baseEnglishDate = null, ?string $equivalentNepaliDate = null): static` — late-static-bound so subclasses work.
  - Constructor accepts optional rows + base dates.
  - Row mutators: `addRow`, `addRows`, `append`, `appendRows`, `prepend`, `prependRows` (fluent, validate year > 0, exactly 12 ints, day-counts 29–32).
  - Queries: `years`, `firstYear`, `lastYear`, `count`, `isEmpty`, `getBaseEnglishDate`, `getEquivalentNepaliDate`.
  - `protected` properties so subclasses can customize; empty base dates by default.
  - Serialization via `__serialize`/`__unserialize` + `Serializable` methods.
- **`src/SystemDataSet.php`** — `extends DataSet`; packaged system calendar (BS 2000–2090):
  - `SystemDataSet::packaged(): self` — full lookup table with system base dates.
  - `DEFAULT_BASE_ENGLISH_DATE` (`1944/01/01`) and `DEFAULT_EQUIVALENT_NEPALI_DATE` (`2000/09/17`).
  - Constructor fills system base dates when omitted.
- **`src/InvalidDataSetException.php`** — thrown for invalid rows / dataset usage.
- **`tests/DataSetTest.php`** — unit coverage for DataSet (rows, mutators, iteration, serialization, custom base dates, subclassing).
- **`tests/DataSetIntegrationTest.php`** — integration with `DateProcessor` and `NepaliDate` (custom datasets, subclasses, SystemDataSet).
- **`tests/TinyCustomDataSet.php`** — test fixture subclass of `DataSet`.
- **README** — “Calendar DataSet” section with custom dataset and subclass examples.

### Changed

- **`src/NepaliDate.php`** — optional `?DataSet $dataSet = null` on `__construct`, `now()`, `make()` so custom calendars can be passed (existing call sites stay valid).
- **`src/Services/Date.php`** — holds `public ?DataSet $dataSet`; constructor accepts optional dataset; `getDateProcessor()` builds `DateProcessor` with that dataset.
- **`src/Services/DateProcessor.php`** — uses a `DataSet` (custom or `SystemDataSet::packaged()` fallback) instead of the old lookup trait; base weekday is a private constant (not on DataSet).
- **`src/Concerns/HasDateConversion.php`** — base dates come from the dataset or `SystemDataSet` defaults; no lookup-trait dependency.
- **`src/Contracts/DateProcessor.php`** — added `getDaysFromBase()`; **removed** `getBaseEnglishDate()` / `getEquivalentNepaliDate()` (read those from `DataSet` instead).
- **`src/Concerns/HasDateOperation.php`** — minor date-parsing cleanup.

### Removed

- **`src/Concerns/HasCalenderLookupTable.php`** — packaged table moved to `SystemDataSet`; trait no longer used.
- **`src/CalendarDataSet.php`** and **`tests/CalendarDataSetTest.php`** — example class; `DataSet` is the real API.
- Weekday support on DataSet (`baseWeekDay`, `getBaseWeekDay`, setters) — weekday stays in `DateProcessor` only.
- From DataSet (never part of the final public surface): `fromLookupTable`, `fromArray`, `toArray`, `remove`, `has`, `getYear`, `getRows`, date setters.

### Migration notes

- **Default behaviour is unchanged.** `NepaliDate::make('2000/09/17')` / `fromADDate` without a dataset still use the packaged system calendar.
- Custom calendars: `DataSet::make($rows, $adBase, $bsBase)` or extend `DataSet`, then pass into `NepaliDate::make` / `fromADDate`.
- Custom `DateProcessor` implementations must implement `getDaysFromBase()` and no longer implement `getBaseEnglishDate` / `getEquivalentNepaliDate`.
- Do not use `HasCalenderLookupTable` or `CalendarDataSet` — they are gone.

### Public API stability (backward compatibility)

The README-documented surface of `NepaliDate` / `Date` must not be removed or renamed (optional extra parameters are fine). See `AGENTS.md` for the protected list.

### Verification

- `vendor/bin/phpunit tests` — 59 tests, 115 assertions, green (only pre-existing PHP 8.4 implicit-nullable deprecations).
