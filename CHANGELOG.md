# Changelog

All notable changes on this branch (`v3.0`) are documented here.

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
