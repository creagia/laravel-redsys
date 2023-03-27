# Changelog

All notable changes to `laravel-redsys` will be documented in this file.

## 2.0.0 - xxx

- New: create payments not related to Eloquent Models with RedsysPayment::createPayment()
- New: define pay method for each Redsys Payment. Bizum.
- Breaking: renamed RedsysNotificationAttempt to RedsysNotificationLog + database changes
- Breaking: the payment amount is stored in cents + database changes
- Breaking: renamed `amount` to `amountInCents` following `creagia/redsys-php`

## 1.1.2 - 2023-02-15

### What's Changed

- Laravel 10.x compatibility by @dtorras in https://github.com/creagia/laravel-redsys/pull/16

**Full Changelog**: https://github.com/creagia/laravel-redsys/compare/1.1.1...1.1.2

## 1.1.1 - 2023-02-08

### What's Changed

- Return normalized status from payment attempt
- Create explicit Redsys notification relationship

### New Contributors

- @dependabot made their first contribution in https://github.com/creagia/laravel-redsys/pull/2

**Full Changelog**: https://github.com/creagia/laravel-redsys/compare/1.1.0...1.1.1

## 1.1 - 2022-02-09

- **CHANGED**: Laravel 9 support

## 1.0.1 - 2022-01-29

- Add exceptions for missing config options

## 1.0.0 - 2022-01-26

Initial release
