# AGENTS.md

Guidance for AI agents working in this repository. Read this before changing code.

## Keep this file up to date

**If you change anything in this repo (API, structure, behaviour, tests, docs), update this file (and `CHANGELOG.md`) in the same change.** Note what changed under “Branch notes” below and, for user-visible changes, in `CHANGELOG.md`.

## What this package is

`dipesh/nepali-date` — Nepali (BS) calendar dates for PHP (>= 8.0): formatting, comparison, manipulation, and AD (Gregorian) ↔ BS conversion. No framework lock-in. Composer autoload: `Dipesh\NepaliDate\` → `src/`, tests → `Tests\` → `tests/`.

## Architecture (current)

| Piece | Role |
|--------|------|
| `NepaliDate` | Public API for working with dates (`extends Date`). Owns `DataSet` + `DateProcessor`, hosts all calculation traits |
| `Services\Date` | System-level date value object: parse, components, format, language, `weekDay` property. No calendar math |
| `Services\DateProcessor` | Day math over a calendar `DataSet` (falls back to `SystemDataSet::packaged()`) |
| `DataSet` | Generic, iterable, serializable calendar data (rows + AD/BS base dates). Subclass for custom calendars |
| `SystemDataSet` | Packaged system calendar (BS 2000–2090) + default base dates (`1944/01/01` ↔ `2000/09/17`) |
| `EnDate` | Lightweight English/AD date helper (not Carbon) |
| Concerns | `HasDateConversion`, `HasDateManipulation`, `HasDateComparison`, `HasDateOperation` (all used by `NepaliDate`) |
| Contracts | `Date`, `DateProcessor`, `Formatter`, `Language` |
| `lang\English`, `lang\Nepali` | Language packs |
| `InvalidDataSetException`, `InvalidDateRangeException` | Errors |

Tests: PHPUnit (`vendor/bin/phpunit tests` or `composer test`). Style: Pint (`composer lint`).

## Backward compatibility — protected public API

**Do not remove or rename the following public API.** README documents it; external callers rely on it. You may *add* optional parameters or new methods, but not break these signatures or names.

### `Dipesh\NepaliDate\NepaliDate` (README + source)

```php
new NepaliDate(string $date = null, Language $language = null, ?DataSet $dataSet = null)
NepaliDate::now(?DataSet $dataSet = null): static
NepaliDate::make(string $date, ?DataSet $dataSet = null): self
NepaliDate::fromADDate(string $date, ?DataSet $dataSet = null): static
$date->create(string $date): static
$date->setLang(string|Language $language): static
$date->__toString(): string
```

### `Dipesh\NepaliDate\Services\Date` (system-level value object)

```php
// Public properties
$date->date;        // string  e.g. "2078/01/01"
$date->year;        // int
$date->month;       // int
$date->day;         // int
$date->weekDay;     // int (real property, 0 = unset; NepaliDate computes it)
$date->language;    // Language
$date->formatter;   // Formatter
Date::$defaultOutputFormat; // '%04d/%02d/%02d'

// Construction / setup
new Date(string $date, Language $language)
$date->setUp(string $date): void
$date->getFormatter(): Formatter
$date->resolveLanguage(string|Language $language): Language
Date::parseComponents(string $date): array  // static, returns [year, month, day]

// Components (language-aware)
$date->year(): int|string
$date->month(string $format = 'm'): int|string
$date->day(): int|string

// Formatting (README characters: Y, m, M, F, d, w, D, l, g)
$date->format(string $format = 'Y/m/d', string|Language|null $lang = null): string
```

### `Dipesh\NepaliDate\NepaliDate` (additional properties \& methods)

```php
// Properties (inherited from Date plus)
$date->dataSet;         // ?DataSet
$date->dateProcessor;   // DateProcessor contract

// Methods
$date->getDateProcessor(): DateProcessor
$date->setUp(string $date): void  // overrides Date::setUp, also recomputes weekDay
```

### Methods mixed into NepaliDate (README “public” ops)

```php
// HasDateConversion
$date->toAd(): EnDate
NepaliDate::fromADDate(string $date, ?DataSet $dataSet = null): static

// HasDateManipulation
$date->addDays(int $day): static
$date->subDays(int $day): static

// HasDateComparison
$date->isEqual(Date|string $date): bool
$date->isGreaterThan(Date|string $date): bool
$date->isLessThan(Date|string $date): bool

// HasDateOperation
$date->weekDay(string $format = 'w'): int|string
$date->diffDays(Date|string $date): int
$date->getTotalDaysFromBaseDate(Date|string $date): int
```

### Dataset API (safe to extend; do not shrink)

```php
DataSet::make(array $rows = [], ?string $baseEnglishDate = null, ?string $equivalentNepaliDate = null): static
new DataSet(array $rows = [], ?string $baseEnglishDate = null, ?string $equivalentNepaliDate = null)
// mutators: addRow, addRows, append, appendRows, prepend, prependRows (fluent)
// queries: years, firstYear, lastYear, count, isEmpty, getBaseEnglishDate, getEquivalentNepaliDate
// also: IteratorAggregate, Serializable

SystemDataSet::packaged(): self
SystemDataSet::DEFAULT_BASE_ENGLISH_DATE      // '1944/01/01'
SystemDataSet::DEFAULT_EQUIVALENT_NEPALI_DATE // '2000/09/17'
```

### Contracts

- `Contracts\DateProcessor`: `getDays`, `getDateFromDays`, `getWeekDayFromDays`
- `Contracts\Formatter`, `Contracts\Language`, `Contracts\Date` — implement when replacing components (see README examples)

## Rules

1. **Never break the protected API above** without an explicit user request (and a CHANGELOG entry).
2. Default behaviour (no custom `DataSet`) must keep matching the packaged system calendar (`2000/09/17` ↔ `1944/01/01`).
3. `DataSet` is generic; packaged data lives only in `SystemDataSet`. Do not put system-specific tables back into `DataSet`.
4. No weekday fields on `DataSet` (weekday lives in `DateProcessor` / formatting only).
5. Custom calendars: users pass `DataSet` (or a subclass) into `NepaliDate` / `DateProcessor`. Prefer adding optional parameters over changing existing ones.
6. Run `vendor/bin/phpunit tests` before finishing; keep the suite green.
7. Update **this file** and **`CHANGELOG.md`** whenever you change code or docs.

## Branch notes

- **Branch `v3.0`** — calendar DataSet refactor. Full detail in [`CHANGELOG.md`](./CHANGELOG.md).
  - Introduced `DataSet` / `SystemDataSet` / `InvalidDataSetException`.
  - Removed `HasCalenderLookupTable` and `CalendarDataSet`.
  - `DateProcessor` contract: removed `getBaseEnglishDate` / `getEquivalentNepaliDate`.
  - Optional `?DataSet` parameter added on `NepaliDate` entry points (BC-safe).
- **Date rewrite** — `Services\Date` is now a pure value object:
  - Removed from `Date`: `$dataSet`, `$dateProcessor`, `getDateProcessor()`, `HasDateOperation` trait, `__get('weekDay')`.
  - `weekDay` is now a real `public int` property (0 = unset; `NepaliDate` computes it).
  - `Date::parseComponents()` (public static) replaces `validateDateAndGetComponents()`.
  - `Date` constructor: `new Date(string $date, Language $language)` — no DataSet param.
  - `NepaliDate` owns `$dataSet`, `$dateProcessor`, `getDateProcessor()`, `HasDateOperation`, and overrides `setUp()` to recompute `weekDay`.
  - `Contracts\DateProcessor`: removed `getDaysFromBase` (kept as `@internal` on concrete `Services\DateProcessor`).
  - `HasDateOperation::getTotalDaysFromBaseDate()` now uses `getDays()` arithmetic instead of `getDaysFromBase()`.
  - `Contracts\Date::month()` signature aligned to `month(string $format = 'm')`.
- Keep this section updated when you land further changes.
