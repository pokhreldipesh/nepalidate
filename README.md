
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

$date = new NepaliDate(language: new \Dipesh\NepaliDate\lang\Nepali()); // Creates current date instance with provided language.
                                                                        
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
```
### Date Manipulation and Comparison Methods

```php
$date->addDays($days);

$date->subDays($days);

$date->weekDay();

$date->isEqual('2048/10/5'); // return true or false

$date->isGreaterThan('2048/10/5');

$date->isLessThan('2048/10/5');
```

### Formatting

Supported format characters: Y, m, M, F, d, w, D, l, g

```php

$date->format('Y-m-d'); // 2050-10-8

$date->format('Y F d g l'); // 2050 Magh 8 Gate Sukrabar

$date->lang('np')->format("Y-m-d, M d g l") // २०५०-१०-२८, माघ २८ गते बिहिबार"
```

## License

Nepali Date is open-sourced package licensed under the [MIT license](https://opensource.org/licenses/MIT).
