# Nepali Date

Nepali (BS) calendar dates for PHP (>= 8.0). Convert between English (AD) and Nepali (BS) calendars, format dates in English or Devanagari, compare and manipulate dates, and plug in custom calendars.

## Installation

```bash
composer require dipesh/nepali-date
```

## Quick Start

```php
use Dipesh\NepaliDate\NepaliDate;

$date = NepaliDate::make('2081-04-25');
echo $date->format('Y F d g l'); // 2081 Shrawan 25 Gate Sukrabar

$date->setLang('np')->format('Y F d g l'); // २०८१ साउन २५ गते शुक्रबार
```

## Creating Instances

```php
use Dipesh\NepaliDate\NepaliDate;

// Current date
$date = NepaliDate::now();

// From a BS date string (flexible separators: / - .)
$date = NepaliDate::make('2081/04/25');
$date = new NepaliDate('2081-04-25');

// From an AD (Gregorian) date
$date = NepaliDate::fromADDate('2024/08/08');

// Immutable: create a new date from an existing one
$other = $date->create('2082/01/01');

// Immutable: switch language
$np = $date->setLang('np');
$np = $date->setLang(new \Dipesh\NepaliDate\lang\Nepali);
```

## Date Conversion

```php
// BS → AD (returns an EnDate, an immutable \DateTime subclass)
$ad = $date->toAd();
echo $ad->format('Y-m-d');

// AD → BS
$nepali = NepaliDate::fromADDate('2024/08/08');
echo $nepali->format('Y/m/d');
```

## Date Components

All component accessors return language-aware formatted values:

```php
$date = NepaliDate::make('2081/04/25');

$date->year();          // "2081"
$date->month('m');      // "04"
$date->month('F');      // "Shrawan"
$date->day();           // "25"
$date->weekDay('w');    // "6"
$date->weekDay('D');    // "Sukra"
$date->weekDay('l');    // "Sukrabar"
```

Raw integer values are available via getters (useful for calculations):

```php
$date->getYear();       // 2081
$date->getMonth();      // 4
$date->getDay();        // 25
$date->getWeekDay();    // 6
$date->getDate();       // "2081/04/25"
```

## Manipulation

All manipulation methods return **new instances** — the original is unchanged.

```php
$date = NepaliDate::make('2081/04/24');

$future = $date->addDays(4);   // 2081/04/28
$past   = $date->subDays(4);   // 2081/04/20

// In-place mutation (only on NepaliDate)
$date->setUp('2082/01/01');
```

## Comparison

```php
$a = NepaliDate::make('2081/04/24');
$b = NepaliDate::make('2081/04/25');

$a->isEqual($b);        // false
$a->isGreaterThan($b);  // false
$a->isLessThan($b);     // true

$a->diffDays($b);       // 1
```

## Formatting

| Character | Description | Example |
|-----------|-------------|---------|
| `Y` | Year (4-digit) | `2081` |
| `m` | Month (numeric, zero-padded) | `04` |
| `M` | Month (short name) | *(empty for English)* |
| `F` | Month (full name) | `Shrawan` |
| `d` | Day (zero-padded) | `25` |
| `w` | Weekday (numeric, 1=Sun–7=Sat) | `6` |
| `D` | Weekday (short name) | `Sukra` |
| `l` | Weekday (full name) | `Sukrabar` |
| `g` | Half-moon indicator | `Gate` / `गते` |

```php
$date->format('Y/m/d');                  // 2081/04/25
$date->format('Y F d g l');              // 2081 Shrawan 25 Gate Sukrabar
$date->format('Y-m-d', 'np');            // २०८१-०४-२५ (temporary language override)
$date->setLang('np')->format('Y F d g l'); // २०८१ साउन २५ गते शुक्रबार
```

## Calendar DataSet

By default the package uses the packaged Nepali calendar (BS 2000–2090). Pass a custom `DataSet` to work with a different calendar.

### Custom DataSet

Each year row is 12 month day-counts (29–32). Base dates anchor AD ↔ BS conversion.

```php
use Dipesh\NepaliDate\DataSet;
use Dipesh\NepaliDate\NepaliDate;

$dataSet = DataSet::make(
    [
        2000 => [31, 32, 31, 32, 31, 30, 30, 30, 29, 30, 29, 31],
        2001 => [31, 31, 32, 31, 31, 31, 30, 29, 30, 29, 30, 30],
    ],
    '1944/01/01', // base AD date
    '2000/09/17', // equivalent BS date
);

$date = NepaliDate::make('2000/09/17', $dataSet);
echo $date->toAd()->format('Y-m-d'); // 1944-01-01
```

### Building incrementally

```php
$dataSet = DataSet::make()
    ->addRow(2000, [31, 32, 31, 32, 31, 30, 30, 30, 29, 30, 29, 31])
    ->append(2001, [31, 31, 32, 31, 31, 31, 30, 29, 30, 29, 30, 30]);
```

### Extending DataSet

Ship a reusable custom calendar by extending `DataSet`:

```php
use Dipesh\NepaliDate\DataSet;

class TinyDataSet extends DataSet
{
    public function __construct(array $rows = [], ?string $baseEnglishDate = null, ?string $equivalentNepaliDate = null)
    {
        parent::__construct(
            $rows ?: [2000 => [30, 30, 30, 30, 30, 30, 30, 30, 30, 30, 30, 30]],
            $baseEnglishDate ?? '2000/01/01',
            $equivalentNepaliDate ?? '2000/01/01',
        );
    }
}

$dataSet = TinyDataSet::make(); // late-static-bound
$date = NepaliDate::make('2000/02/01', $dataSet);
```

DataSets are iterable and serializable:

```php
foreach ($dataSet as $year => $monthDays) { /* ... */ }
$restored = unserialize(serialize($dataSet));
```

---

## Architecture

```
┌─────────────────────────────────────────────────────────────┐
│  NepaliDate (public API — mutable via setUp)                 │
│  ├── HasDateComparison, HasDateConversion                    │
│  ├── HasDateManipulation, HasDateOperation                   │
│  ├── DataSet + DateProcessor                                 │
│  └── extends Date (immutable value object)                   │
├─────────────────────────────────────────────────────────────┤
│  Contracts                                                   │
│  ├── Date          (withDate, getters, format, parse)        │
│  ├── Formatter     (format, formatNumber, formatMonth, …)    │
│  ├── Language      (digits, weeks, months, gate)              │
│  └── DateProcessor (getDays, getDateFromDays, getWeekDay)    │
├─────────────────────────────────────────────────────────────┤
│  Services                                                    │
│  ├── Date           — pure value object (no calendar math)   │
│  ├── Formatter      — abstract base (Date on construct)      │
│  ├── FormatDate     — default BS formatter                   │
│  └── DateProcessor  — day math over a DataSet                │
├─────────────────────────────────────────────────────────────┤
│  lang                                                        │
│  ├── English       — Romanized language pack (constants)     │
│  └── Nepali        — Devanagari language pack (constants)    │
├─────────────────────────────────────────────────────────────┤
│  DataSet / SystemDataSet    — calendar data (fluent builder) │
│  EnDate                     — immutable AD date helper       │
└─────────────────────────────────────────────────────────────┘
```

### Design patterns

| Component | Pattern |
|-----------|---------|
| `Services\Date` | Value Object (immutable, `withDate()` factory) |
| `Services\Formatter` | Template Method (abstract base) |
| `Services\FormatDate`, `lang\*`, `Contracts\Language` | Strategy |
| `Services\DateProcessor` | Strategy |
| `DataSet` | Builder (fluent mutators) |
| `EnDate` | Value Object (immutable arithmetic) |
| `NepaliDate` | Facade (public API) |

### Extending and Customizing

Override `getFormatter()`, `getDateProcessor()`, or `resolveLanguage()` on a `NepaliDate` subclass:

```php
class CustomDate extends \Dipesh\NepaliDate\NepaliDate
{
    public function getFormatter(): \Dipesh\NepaliDate\Contracts\Formatter
    {
        return new CustomFormatter($this);
    }

    public function getDateProcessor(): \Dipesh\NepaliDate\Contracts\DateProcessor
    {
        return new CustomDateProcessor($this->dataSet);
    }
}
```

#### Custom Formatter

Extend `Services\Formatter` (receives `Date` on construct):

```php
use Dipesh\NepaliDate\Services\Formatter;

class CustomFormatter extends Formatter
{
    public function format(string $format): string { /* ... */ }
    public function formatMonth(string $format = 'm'): mixed { /* ... */ }
    public function formatWeekDay(string $format = 'w'): mixed { /* ... */ }
}
```

#### Custom DateProcessor

```php
use Dipesh\NepaliDate\Contracts\DateProcessor;

class CustomDateProcessor implements DateProcessor
{
    public function getDays(int $year, int $month, int $day): int { /* ... */ }
    public function getDateFromDays(int $totalDays): string { /* ... */ }
    public function getWeekDayFromDays(int $days): int { /* ... */ }
}
```

#### Custom Language

```php
use Dipesh\NepaliDate\Contracts\Language;

class CustomLanguage implements Language
{
    public function getGate(): string { return ''; }
    public function getDigit(int $digit): int|string { return $digit; }
    public function getWeek(int $week): array { return ['l' => 'Sunday', 'D' => 'Sun']; }
    public function getMonth(int $month): array { return ['F' => 'January', 'M' => 'Jan']; }
}
```

---

## Testing

Tests use [Pest](https://pestphp.com). Run the full suite:

```bash
composer test
# or
vendor/bin/pest tests --colors
```

Run code style checks with [Pint](https://laravel.com/docs/pint):

```bash
composer lint
```

---

## Recommended Package for Full [Calendar](https://github.com/pokhreldipesh/calendar) System

For a complete calendar system with events, navigation, and more, see **[dipesh/calendar](https://github.com/pokhreldipesh/calendar)**:

```bash
composer require dipesh/calendar
```

## License

Nepali Date is open source under the [MIT license](https://opensource.org/licenses/MIT).
