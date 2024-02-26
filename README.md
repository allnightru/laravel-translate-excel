# Convert translations to/from a single Excel file

[![Latest Version on Packagist](https://img.shields.io/packagist/v/ins/laravel-translate-excel.svg?style=flat-square)](https://packagist.org/packages/ins/laravel-translate-excel)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/ins/laravel-translate-excel/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/ins/laravel-translate-excel/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/ins/laravel-translate-excel/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/ins/laravel-translate-excel/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/ins/laravel-translate-excel.svg?style=flat-square)](https://packagist.org/packages/ins/laravel-translate-excel)

This package converts all PHP translations from Laravel`s "lang" directory a single Excel file with
dot-arrayed keys. Each file comes to a separate sheet. 

## Installation

You can install the package via composer:

```bash
composer require ins/laravel-translate-excel
```

You can publish the config file with:

```bash
php artisan vendor:publish --tag="laravel-translate-excel-config"
```

This is the contents of the published config file:

```php
return [
    // this is the default locale which will be used as reference
    'main_locale' => 'en',
    // what locations would you like to have in your file
    'locales' => ['en','de','ru','ja']
];
```

## Usage

Just invoke the artisan console command:

```php
php artisan lang:convert
```

And get your generated file at 'storage/app/translations.xlsx'
You can specify filename as a parameter:

```php
php artisan lang:convert to my_filename.xlsx
```

After you translate the file in Excel you would probably need to get it back to your app.
So put your translated file "my_filename.xlsx" into "storage/app" directory, then run:

```php
php artisan lang:convert from my_filename.xlsx
```

PHP-translation files will be generated into "storage/lang" folder.
Just copy them back to your app root. 


## Changelog

Readme update & config publish

## Security Vulnerabilities

This package was made just for my needs, so it was not tested at all and the code is not perfect also.
Use it at your own risk.

## Credits

- [Kirill Petrov](https://github.com/ins)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
