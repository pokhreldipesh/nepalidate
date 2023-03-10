# Nepali Date

Nepali date is php package for working with nepali date. This package supports nepali date formatting nicely according to your needs. Following are the key features of this package:

- Convert english date (AD) to nepali(BS).
- Convert nepali(BS) to english date (AD).
- Supports various nepali date formatting.
- Extensible

## Installation

```
composer require dipesh/nepali-date
```

### Initialize nepali date

```php
$date = new NepaliDate('1994/01/21');

//or
$date = new NepaliDate();

```

### Date conversion

```php
$date->toNepali(); // output 2050/10/8
//or
$date->toNepali('1994/01/21');

$date->toEnglish(); // 1994/01/21
//or
$date->toEnglish('2050/10/8');
```

### Other functions for nepali date

```php
$date->toNepali()->addDays(10);

$date->toNepali()->subDays(10);

$date->toNepali()->addMonths(1);

$date->toNepali()->subMonths(2);

$date->toNepali()->subYears(2);

$date->toNepali()->diff($date2); // returns days

$date->toNepali()->weekDay();

$date->toNepali()->day();

$date->toNepali()->month();

$date->toNepali()->year();
```

### Formatting

```php
// Suported format Y, m, M, F, d, w, D, l, g

$date->toNepali()->format('Y-m-d'); // 2050-10-8

$date->toNepali()->format('Y F d g l'); // 2050 Magh 8 Gate Sukrabar
```

### Comparision

```php
$date->isEqual('2048/10/5'); // return true or false

$date->isGreaterThan('2048/10/5');

$date->isGreaterThanOrEqual('2048/10/5');

$date->isLessThan('2048/10/5');

$date->isLessThanOrEqual('2048/10/5');

```

### Local languages

Currently nepali and english language are available but this is not limited, you can use your own language by using callback function.

```php
$date->toNepali()->lang('np')->format('Y-m-d'); // २०५०-१०-८

$date->toNepali()->lang('np')->format('Y F d g l'); // २०५० माघ ८ गते शुक्रबार

//You can also add your own language
$date->toNepali()->lang(function($lang) {
  return $lang; // Provide your own language array
})->format('Y F d g l');
```

### Push data to lookup table

```php
$newDateInstance = (new NepaliDate())->pushLookUpTable(['2091' => [30, 32, 31, 32, 31, 30, 30, 30, 29, 30, 30, 30]]);
```

## License

Nepali Date is open-sourced package licensed under the [MIT license](https://opensource.org/licenses/MIT).
