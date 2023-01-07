# Nepali Date

Nepali date is php package written for convert nepali date to english and english to nepali. This package supports nepali date formatting nicely according to your needs. Following are the key features of this package:

- Convert english date (AD) to nepali(BS).
- Convert nepali(BS) to english date (AD).
- Supports various nepali date formatting.
- Extensible

This is the powerfull nepali date package based on searching algorithm till date.

## How to use

### Initialize nepali date

```php
$date = new NepaliDate('1994/01/21');
```

### Date conversion

```php
$date->toNepali(); // output 2050/10/8

$date->toEnglish(); // 1994/01/21
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

### Local languages

Currently nepali and english language are supported. You can use your own language by using callback function.

```php
$date->toNepali()->lang('np')->format('Y-m-d'); // २०५०-१०-८

$date->toNepali()->lang('np')->format('Y F d g l'); // २०५० माघ ८ गते शुक्रबार

//You can also add your own language
$date->toNepali()->lang(function($lang) {
  return $lang; // Provide your own language array
})->format('Y F d g l');
```

## License

Nepali Date is open-sourced package licensed under the [MIT license](https://opensource.org/licenses/MIT).
