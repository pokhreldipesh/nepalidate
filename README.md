
[//]: # (<p align="center"><img src="./calendar.png"  alt="Laravel Logo"></p>)

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

//or
$date = NepaliDate::make("2070-8-20"); // Creates date instance with default language configuration

//or
$date = NepaliDate::now(); // Creates current date instance

// Work with global instance 
$date->create($date); // Creates an immutable date instance while retaining the previous configuration settings.

```

### Date Conversion

```php
$date->toAd(); // output Carbon date format
//or
$date = NepaliDate::fromADDate("1990-9-10");
```
### Date Component Retrieval
```php
$date->year();   // Retrieves the year based on the language configuration
$date->month();  // Retrieves the month based on the language configuration
$date->day();    // Retrieves the day based on the language configuration
$date->weekDay(); // Retrieves the day week day

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

| Format Character | Description                                    | Example Output                        |
|------------------|------------------------------------------------|---------------------------------------|
| `Y`              | Year (4-digit format)                          | `2078`                                |
| `m`              | Month (Numeric, zero-padded, 01-12)            | `01` for January, `12` for December   |
| `M`              | Month (Short textual representation)           | `Jan` for January, `Dec` for December |
| `F`              | Month (Full textual representation)            | `January`, `December`                 |
| `d`              | Day of the month (Numeric, zero-padded, 01-31) | `01` for the 1st, `31` for the 31st   |
| `w`              | Day of the week (Numeric, 1-7)                 | `1` for Sunday, `7` for Saturday      |
| `D`              | Day of the week (Short textual representation) | `Sun` for Sunday, `Wed` for Saturday  |
| `l`              | Day of the week (Full textual representation)  | `Sunday`, `Wednesday`                 |
| `g`              | This is not for english(AD) format             | `Gate` or `गते`                       |

```php

$date->format('Y-m-d'); // 2050-10-8

$date->format('Y F d g l'); // 2050 Magh 8 Gate Sukrabar

$date->format("Y-m-d, M d g l") // २०५०-१०-२८, माघ २८ गते बिहिबार"

//or
$date->format("Y-m-d, M d g l", 'np')
```

## Recommended Package for Full Calendar System

For developers looking to create a comprehensive calendar system, we recommend the **dipesh/calendar** package. This package provides an easy-to-use interface for managing a full Nepali calendar, allowing you to seamlessly add events, navigate through months and years, and much more.

To install the package, run:

```bash
composer require dipesh/calendar
```

## License

Nepali Date is open-sourced package licensed under the [MIT license](https://opensource.org/licenses/MIT).
