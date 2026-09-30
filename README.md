# Nepali Date

The Nepali Date package is designed for working with the Nepali calendar. It provides functionality for converting dates between the English (AD) and Nepali (BS) calendars, along with a wide range of methods for handling and manipulating Nepali dates. This comprehensive tool facilitates seamless integration and operations within the Nepali calendar system.

## Installation

To install the package, use Composer:

```
composer require dipesh/nepali-date
```

### Uses

Creating Instances

```php
use Dipesh\NepaliDate\NepaliDate;

$date = new NepaliDate(language: 'np or en'); // Creates current date instance with provided language.

//or
$date = $date->setLang('np') or $date->setLang(new \Dipesh\NepaliDate\lang\Nepali()) // Creates immutable instance

//or
$date = new NepaliDate("2050-8-10") // Creates date instance with default language configuration
//You are free to use either of these formats, e.g., yyyy-mm-dd or yyyy/mm/dd." 
//or
$date = NepaliDate::make("2070-8-20"); // Creates date instance with default language configuration

//or
$date = NepaliDate::now(); // Creates current date instance

// Work with global instance
$date->create($date); // Creates an immutable date instance while retaining the previous configuration settings.

```

### Date Conversion

```php
$date->toAd(); // creates php Date instance
//or
$date = NepaliDate::fromADDate("1990-9-10");
```

### Calendar DataSet

By default the package uses the packaged Nepali calendar. Pass your own calendar data to any conversion API to work with a custom dataset.

#### Custom DataSet with your own rows

Each year row is 12 month day-counts (29–32). Base dates anchor AD ↔ BS conversion.

```php
use Dipesh\NepaliDate\DataSet;
use Dipesh\NepaliDate\NepaliDate;

$dataSet = DataSet::make(
    [
        2000 => [31, 32, 31, 32, 31, 30, 30, 30, 29, 30, 29, 31],
        2001 => [31, 31, 32, 31, 31, 31, 30, 29, 30, 29, 30, 30],
    ],
    '1944/01/01', // base English (AD) date that matches the BS base date below
    '2000/09/17', // equivalent Nepali (BS) date
);

$date = NepaliDate::make('2000/09/17', $dataSet);
$date->toAd()->format('Y-m-d'); // 1944-01-01

$date = NepaliDate::fromADDate('1944/01/01', $dataSet);
$date->format('Y/m/d'); // 2000/09/17
```

You can also build a dataset incrementally:

```php
$dataSet = DataSet::make()
    ->addRow(2000, [31, 32, 31, 32, 31, 30, 30, 30, 29, 30, 29, 31])
    ->append(2001, [31, 31, 32, 31, 31, 31, 30, 29, 30, 29, 30, 30]);
```

Data sets are iterable and serializable:

```php
foreach ($dataSet as $year => $monthDays) {
    // ...
}

$restored = unserialize(serialize($dataSet));
```

#### Extending DataSet

Ship a reusable custom calendar by extending `DataSet`. Override the constructor to provide your own rows and base dates.

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

$dataSet = new TinyDataSet; // or TinyDataSet::make()
$date = NepaliDate::make('2000/02/01', $dataSet);
```

`make()` is late-static-bound, so `TinyDataSet::make()` returns a `TinyDataSet`.

### Date Component Retrieval Based on the Language Configuration
```php
$date->year();   // Retrieves the year
$date->month($format);  // Retrieves the formatted month
$date->day();    // Retrieves the day
$date->weekDay($format); // Retrieves the formatted week day
```
### Date Manipulation and Comparison Methods

```php
$date->addDays($days);

$date->subDays($days);

$date->isEqual('2048/10/5'); // return true or false

$date->isGreaterThan('2048/10/5');

$date->isLessThan('2048/10/5');
```

### Formatting

Supported format characters: Y, m, M, F, d, w, D, l, g

| Format Character | Description                                    | Example Output                            |
|------------------|------------------------------------------------|-------------------------------------------|
| `Y`              | Year (4-digit format)                          | `2078`                                    |
| `m`              | Month (Numeric, zero-padded, 01-12)            | `01` for Baisakh, `12` for Chait          |
| `M`              | Month (Short textual representation)           | `Bai` for Baisakh, `Dec` for Chai         |
| `F`              | Month (Full textual representation)            | `Baisakh`, `Jeth`                         |
| `d`              | Day of the month (Numeric, zero-padded, 01-31) | `01` for the 1st, `31` for the 31st       |
| `w`              | Day of the week (Numeric, 1-7)                 | `1` for Aaitabar, `7` for Sanibar         |
| `D`              | Day of the week (Short textual representation) | `Aaita` for Aaitabar, `Budh` for Budhabar |
| `l`              | Day of the week (Full textual representation)  | `Aaitabar`, `Sombar`                      |
| `g`              | This is not for english(AD) format             | `Gate` or `गते`                           |

```php

$date->format('Y-m-d'); // 2050-10-8

$date->format('Y F d g l'); // 2050 Magh 8 Gate Sukrabar

$date->format("Y-m-d, M d g l") // २०५०-१०-२८, माघ २८ गते बिहिबार"

//or
$date->format("Y-m-d, M d g l", 'np')
```

## Recommended Package for Full [Calendar](https://github.com/pokhreldipesh/calendar) System

For developers looking to create a comprehensive [Calendar](https://github.com/pokhreldipesh/calendar) system, we recommend the **dipesh/calendar** package. This package provides an easy-to-use interface for managing a full Nepali calendar, allowing you to seamlessly add events, navigate through months and years, and much more.

To install the package, run:

```bash
composer require dipesh/calendar
```

---
## Extending and Customizing the Nepali Date Package

This package is designed for great extensibility, allowing you to customize key components to fit your specific needs. The package is built around three main concepts:

1. **DateProcessor**: Handles all date-related calculations and logic.
2. **Language**: Manages language-specific aspects, such as number formatting and month names.
3. **Formatter**: Controls how dates are formatted and displayed.

You can extend or replace these components with your own implementations, enabling you to modify the core logic without touching the existing codebase. Below are examples of how to achieve this customization:

### Example: Extending the Nepali Date Class

```php
// Extending the main NepaliDate class
class CustomDate extends \Dipesh\NepaliDate\NepaliDate
{
    // Your new feature implementation goes here

    // Use a custom date processor for all date-related logic
    public function getDateProcessor()
    {
        return new CustomDateProcessor();
    }

    // Use a custom formatter for all date formatting
    public function getFormatter()
    {
        return new CustomFormatter();
    }
}
```

### Example: Creating a Custom DateProcessor

```php
// Implementing a custom DateProcessor
class CustomDateProcessor implements \Dipesh\NepaliDate\Contracts\DateProcessor
{
    public function getDays(int $year, int $month, int $day): int
    {
        // Your custom logic for calculating days
    }

    public function getDaysFromBase(int $year, int $month, int $day): int
    {
        // Days from your dataset's equivalent Nepali base date (0 on that date)
    }

    public function getDateFromDays(int $totalDays): string
    {
        // Your custom logic for calculating a date from total days
    }

    public function getWeekDayFromDays(int $days): int
    {
        // Your custom logic for determining the weekday from days
    }
}
```

### Example: Creating a Custom Formatter

```php
// Implementing a custom Formatter
class CustomFormatter implements \Dipesh\NepaliDate\Contracts\Formatter
{
    public function setUp(Date $date): static
    {
        // Setup logic with the date
    }

    public function format(string $format): string
    {
        // Your custom logic for formatting the date
    }

    public function formatNumber(int $number): string
    {
        // Your custom logic for formatting numbers
    }

    public function formatMonth(string $format = 'm'): mixed
    {
        // Your custom logic for formatting months
    }

    public function formatWeekDay(string $format = 'w'): mixed
    {
        // Your custom logic for formatting weekdays
    }
}
```

### Example: Creating a Custom Language

```php
// Implementing a custom language
class CustomLanguage implements \Dipesh\NepaliDate\Contracts\Language
{
    public function getGate(): string
    {
        // Your custom language specific gate especially useful for nepali language and you might not need this
        // Eg: return "";
    }

    public function getDigit(int $digit):int|string
    {
        // Your custom language specific digit
        // Eg: return 1;
    }

    public function getWeek(int $week):array
    {
        // Your custom language specific week day
        // Eg: return ['l' => 'Sunday', 'D' => 'Sun'];
    }

    public function getMonth(int $month):array
    {
        // Your custom language specific month
        // Eg: return ['F' => 'January', 'M' => 'Jan'];
    }
}
```
---
## License

Nepali Date is open-sourced package licensed under the [MIT license](https://opensource.org/licenses/MIT)
