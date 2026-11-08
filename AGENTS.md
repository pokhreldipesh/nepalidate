# AGENTS.md

Guidance for AI agents working in this repository. Read this before changing code.

## Keep this file up to date

**If you change anything in this repo (API, structure, behaviour, tests, docs), update this file (and `CHANGELOG.md`) in the same change.** Note what changed under “Branch notes” below and, for user-visible changes, in `CHANGELOG.md`.

## What this package is

`dipesh/nepali-date` — Nepali (BS) calendar dates for PHP (>= 8.4): formatting, comparison, manipulation, and AD (Gregorian) ↔ BS conversion. No framework lock-in. Composer autoload: `Dipesh\NepaliDate\` → `src/`, tests → `Tests\` → `tests/`.

## Architecture (current)

| Piece | Role | Pattern |
|--------|------|---------|
| `NepaliDate` | Public API. **Composes** a `Dipesh\Date\Date` (`$date`), delegates date-level work to it, owns `DataSet` + `DateProcessor` and the calculation traits. Mutable via `setUp()` | Facade + mutable state |
| `Dipesh\Date\*` (dependency `dipesh/date`) | System-level date value object: parse, components, format, language. Immutable | Value Object |
| `Services\DateProcessor` | Day math over a calendar `DataSet` | Strategy |
| `Contracts\DateProcessor` | Processor interface: `getDays`, `getDateFromDays`, `getWeekDayFromDays` | Contract |
| `DataSet` | Fluent mutable calendar data container (rows + AD/BS base dates) | Builder |
| `SystemDataSet` | Packaged calendar (BS 2000–2090) + default base dates | Factory |
| `EnDate` | Immutable AD date helper (returns new instances from all arithmetic) | Value Object |
| Concerns | `HasDateConversion`, `HasDateManipulation`, `HasDateOperation` | Mixins |
| `InvalidDataSetException`, `InvalidDateRangeException` | Domain exceptions | — |

The system-level date layer (`Date`, `Formatter`, `FormatDate`, `Language`, `LanguagePack`, `lang\*`, `Contracts\{Date,Formatter,Language,LanguagePack}`) lives in the published package **`dipesh/date`** (`^1.0` from Packagist). It is not duplicated here.

**Composition, not inheritance.** `NepaliDate` *has-a* `Date` (exposed as the `$date` property) rather than extending it. Date-level methods and properties are forwarded from `NepaliDate` via `__call()` / `__get()` / `__callStatic()`, and their surface is declared in the class docblock's `@method` / `@property-read` list. Keep that docblock in sync when `dipesh/date` changes.

Tests: Pest (`vendor/bin/pest tests` or `composer test`). Style: Pint (`composer lint`). Static analysis: PHPStan (`composer stan`). Refactoring: Rector (`composer rector`).

## Backward compatibility — protected public API

**Do not remove or rename the following public API.** README documents it; external callers rely on it. You may *add* optional parameters or new methods, but not break these signatures or names.

### `Dipesh\NepaliDate\NepaliDate` (README + source)

```php
new NepaliDate(?string $date = null, LanguagePack|Language|null $language = null, ?DataSet $dataSet = null)
NepaliDate::now(?DataSet $dataSet = null): static
NepaliDate::make(string $date, ?DataSet $dataSet = null): self
NepaliDate::fromADDate(string $date, ?DataSet $dataSet = null): static
$date->create(string $date): static
$date->withDate(string $date): static   // preserves dataSet/language/custom formatter + recomputes weekDay
$date->setUp(string $date): void        // mutator: re-parse in place + recompute weekDay
$date->setLang(string|LanguagePack|Language $language): static
$date->formatUsing(Formatter|callable $formatter): static
$date->languageUsing(LanguagePack|Language|callable $language): static
$date->__toString(): string             // $date->date->getDate()
```

`Language` is `Dipesh\Date\Language` (the bridge); `LanguagePack` is `Dipesh\Date\Contracts\LanguagePack`;
`Formatter` is `Dipesh\Date\Contracts\Formatter`. All come from `dipesh/date`.

`formatUsing()` / `languageUsing()` deliberately return `static` (not the composed `Date`) so
fluent chains keep calendar methods.

### Composed `Date` — `$date->date`

`$nepali->date` **is the composed `Dipesh\Date\Date` object** (not a string). Reach the normalized
date string via `$nepali->date->getDate()` or `$nepali->getDate()`.

Date-level methods and properties are forwarded from `NepaliDate` through `__call()`, `__get()`,
and `__callStatic()`. The full forwarded surface is declared in `NepaliDate`'s docblock
(`@method` / `@property-read`) — **keep that list accurate** when `dipesh/date` changes.

Forwarded (see the docblock for the authoritative list):

```php
// properties (via __get)
$date->year; $date->month; $date->day; $date->weekDay; $date->language; $date->formatter

// methods (via __call)
$date->getDate(): string
$date->getYear(): int / getMonth(): int / getDay(): int / getWeekDay(): int
$date->format(string $format = 'Y/m/d', string|LanguagePack|Language|null $lang = null): string
$date->getLanguage(): Language / getFormatter(): Formatter
$date->resolveLanguage(string|LanguagePack|Language $language): Language
$date->isEqual(NepaliDate|Date|string $date): bool      // also isGreaterThan / isLessThan

// static (via __callStatic)
NepaliDate::parseComponents(string $date): array        // [year, month, day]
```

`NepaliDate` arguments are unwrapped to their composed `Date` before delegation, so comparisons
accept either.

`isEqual` / `isGreaterThan` / `isLessThan` come from `Dipesh\Date\Concerns\HasComparisonDate`
(component-wise ordering — no calendar arithmetic).

### Methods defined on NepaliDate

```php
// Calendar-aware properties
$date->dataSet;         // ?DataSet
$date->dateProcessor;   // DateProcessor contract

// Components (language-aware) — kept here, not in dipesh/date
$date->year(): int|string
$date->month(string $format = 'm'): int|string
$date->day(): int|string

// HasDateConversion
$date->toAd(): EnDate
NepaliDate::fromADDate(string $date, ?DataSet $dataSet = null): static

// HasDateManipulation
$date->addDays(int $day): static
$date->subDays(int $day): static

// HasDateComparison — isAnniversary / isBirthday are defined here (not in dipesh/date):
// isBirthday does not exist there, and its isAnniversary() requires an explicit argument
// because that package has no clock. Ordering ops (isEqual/isGreaterThan/isLessThan) are
// delegated to Dipesh\Date\Concerns\HasComparisonDate.
$date->isAnniversary(NepaliDate|Date|string|null $date = null): bool  // $date defaults to NepaliDate::now()
$date->isBirthday(NepaliDate|Date|string|null $date = null): bool     // alias for isAnniversary()

// HasDateOperation
$date->weekDay(string $format = 'w'): int|string
$date->diffDays(NepaliDate|Date|string $date): int
$date->getTotalDaysFromBaseDate(NepaliDate|Date|string $date): int

// Infrastructure
$date->getDateProcessor(): DateProcessor
```

In all of the above, `Date` means `Dipesh\Date\Contracts\Date` (from `dipesh/date`).

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

- `Contracts\DateProcessor` (this package): `getDays`, `getDateFromDays`, `getWeekDayFromDays`
- `Dipesh\Date\Contracts\Date`, `Formatter`, `Language`, `LanguagePack` (dependency `dipesh/date`) — see that package for the current surface. `Language`/`LanguagePack` is the extension point for custom languages (implement `LanguagePack`, not a lookup class).
- `Dipesh\Date\Formatter` — abstract base class; takes `Date` on construct. Extend for custom formatters.

## Rules

1. **Never break the protected API above** without an explicit user request (and a CHANGELOG entry).
2. Default behaviour (no custom `DataSet`) must keep matching the packaged system calendar (`2000/09/17` ↔ `1944/01/01`).
3. `DataSet` is generic; packaged data lives only in `SystemDataSet`. Do not put system-specific tables back into `DataSet`.
4. No weekday fields on `DataSet` (weekday lives in `DateProcessor` / formatting only).
5. Custom calendars: users pass `DataSet` (or a subclass) into `NepaliDate` / `DateProcessor`. Prefer adding optional parameters over changing existing ones.
6. Run `vendor/bin/pest tests` before finishing; keep the suite green.
7. Update **this file** and **`CHANGELOG.md`** whenever you change code or docs.
8. **Do not re-add a local date layer.** Parsing/formatting/language belong to `dipesh/date`. This package only adds calendar awareness (DataSet, day math, AD ↔ BS).

## Branch notes

- **`dipesh/date` extraction — local date layer removed, composition over inheritance.** `NepaliDate` **composes** a `Dipesh\Date\Date` (published package `dipesh/date: ^1.0`) exposed as the `$date` property. It does *not* extend it.
  - **Deleted** (now owned by `dipesh/date`): `Services\Date`, `Services\Formatter`, `Services\FormatDate`, `Contracts\Date`, `Contracts\Formatter`, `Contracts\Language`, `lang\English`, `lang\Nepali`, `Concerns\HasDateComparison`, and `tests/DateTest.php` (those 41 cases now live in the dependency's own suite).
  - **Why composition.** `NepaliDate` is a calendar-aware facade; the date value object is a replaceable dependency. Date-level work is reached through `$nepali->date`, and forwarded from the facade via `__call()` / `__get()` / `__callStatic()` so existing call sites keep working.
  - **`$nepali->date` is the composed `Date` object, not the normalized string.** Reach the string via `$nepali->date->getDate()` or `$nepali->getDate()`.
  - **Docblock is the delegation contract.** `NepaliDate`'s `@method` / `@property-read` list declares everything forwarded to `Date`. Keep it in sync with `dipesh/date`.
  - **Breaking type changes** (explicitly requested): `Dipesh\NepaliDate\Services\Date`, `Dipesh\NepaliDate\Contracts\{Date,Formatter,Language}`, `Dipesh\NepaliDate\lang\{English,Nepali}` no longer exist — use `Dipesh\Date\*` equivalents.
  - `NepaliDate::__construct` / `setLang()` language type widened: `Language $language` → `LanguagePack|Language $language` (`Language` = `Dipesh\Date\Language`, `LanguagePack` = `Dipesh\Date\Contracts\LanguagePack`). Custom language classes now implement `LanguagePack` (data-only `toArray()`), not a lookup interface.
  - `year()` / `month()` / `day()` (language-aware accessors) stay on `NepaliDate` — `dipesh/date`'s `Date` does not provide them.
  - `isEqual` / `isGreaterThan` / `isLessThan` are delegated to `Dipesh\Date\Concerns\HasComparisonDate` (component-wise ordering) instead of day-math via `DateProcessor`. Equivalent for in-range dates; also works outside the calendar range. `__call()` unwraps `NepaliDate` arguments to their composed `Date` first, so comparisons accept either.
  - `isAnniversary()` (nullable, defaults to today) and `isBirthday()` are defined on `NepaliDate`, not in `dipesh/date` — that package has no clock and no `isBirthday`.
  - `formatUsing()` / `languageUsing()` return `static` (not the composed `Date`) so fluent chains keep calendar methods.
  - `NepaliDate::withDate()` / `setUp()` rebuild the composed `Date` and re-derive its weekday via `Date::withWeekDay()`.
- **`Date::withWeekDay(int $weekDay): static`** added to `dipesh/date`. Required by composition: `Date::$weekDay` is `protected(set)`, so a *composing* class cannot write it (only subclasses could). Without it, weekday formatting would render `0`. Belongs with `withDate()` / `formatUsing()` / `languageUsing()`.
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
  - `Contracts\Date` expanded: added `getDate()`, `getYear()`, `getMonth()`, `getDay()`, `getWeekDay()`, `getLanguage()`, `format()`, `getFormatter()`, `resolveLanguage()`, `parseComponents()`. Internal code (`FormatDate`, `HasDateOperation`) uses these getters instead of direct property access.
  - `Date` is now immutable: `setUp()` removed, replaced by `withDate(): static` (returns new instance). `assignDate()` is `protected` for subclass init.
  - `NepaliDate` adds `setUp()` as public mutator and overrides `withDate()` to preserve DataSet and recompute weekDay. `create()` and `addDays()` use `withDate()` instead of `clone + setUp`.
  - Formatter system refactored: `setUp()` removed from `Contracts\Formatter`. `Services\Formatter` (abstract) takes `Date` on construct. `FormatDate` extends it. `Date::__clone` recreates formatter. `format()` no longer re-binds formatter.
  - Full maintainability refactor: language packs use class constants (was `public static`), `EnDate` arithmetic returns new instances (was mutating), `DateProcessor` no longer depends on `Date` class (has own `DATE_FORMAT` constant), `Date::format()` language override is temporary (doesn't persist). `FormatDate` uses named `const` arrays for format groups.
- **`dipesh/date` now installed from Packagist** — `composer.json` requires **`dipesh/date: ^1.0`** (published v1.0.0, `github.com/pokhreldipesh/date`). The earlier Composer **path repository** (`../date`, symlinked) has been removed; `vendor/dipesh/date` is now a real directory extracted from the GitHub archive. No local path repo should be re-added.
- **`pint.json` added** — disables `fully_qualified_strict_types` so FQCNs in docblocks survive formatting. This was required because `NepaliDate`'s `@method` / `@property-read` / `@param` tags name `\Dipesh\Date\…` types explicitly; Pint's fixer was shortening them back to bare names that resolve against `Dipesh\NepaliDate\` and look like missing classes.
- **`isAnniversary`** — new `HasDateComparison` predicate: true when month + day match, regardless of year (a date is its own anniversary). Optional `$date` argument defaults to `NepaliDate::now()` (today) when omitted. Added to README Comparison section and `tests/ComparisonTest.php`.
- **`isBirthday`** — convenience alias for `isAnniversary()` (same signature/behaviour). Documented in README and tested in `tests/ComparisonTest.php`.
- Keep this section updated when you land further changes.
