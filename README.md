# Laravel Quran

<p align="center">
    <a href="https://packagist.org/packages/jhonoryza/laravel-quran">
        <img src="https://poser.pugx.org/jhonoryza/laravel-quran/d/total.svg" alt="Total Downloads">
    </a>
    <a href="https://packagist.org/packages/jhonoryza/laravel-quran">
        <img src="https://poser.pugx.org/jhonoryza/laravel-quran/v/stable.svg" alt="Latest Stable Version">
    </a>
    <a href="https://packagist.org/packages/jhonoryza/laravel-quran">
        <img src="https://poser.pugx.org/jhonoryza/laravel-quran/license.svg" alt="License">
    </a>
</p>

## install

```bash
composer require jhonoryza/laravel-quran
```

## run migration

```bash
php artisan migrate
```

this will create 2 tables: qurans and quran_verses

## publish config file

```bash
php artisan vendor:publish --tag=quran-config
```

Source of quran.

options: 'kemenag', 'kemenag_official', 'tanzil.net'

  - kemenag is using rest api method to get the data from non official source
  - kemenag_official is using rest api method to get the data from official source
  - tanzil.net is using dump sql data that provided by tanzil to get the data

## sync data

sync quran data

```bash
php artisan quran:sync
```

### Security

If you've found a bug regarding security please mail [jardik.oryza@gmail.com](mailto:jardik.oryza@gmail.com) instead of
using the issue tracker.

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
