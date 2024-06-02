# Changelog

All notable changes to `laravel-redsys` will be documented in this file.

## 3.0.0 - 2024-06-02

- New: Local gateway is refactored to work with Sail, `artisan serve` or any other single threaded server.
- New: Improved REST integration with handled responses.
- Breaking: Updated database schema to allow different transaction types (refunds for example) with the same order number.

Updated main dependency:

- Breaking: creagia/redsys-php v3 compatibility. Check the [changelog](https://github.com/creagia/redsys-php/blob/main/CHANGELOG.md).

## 2.0.0 - 2023-05-16

This version is a complete rewrite. Though there are lots of breaking changes, all features of v1 are retained.
Notable changes and additions:

- New: Manage bank cards as tokens with Credential-on-File requests
- New: Redirection and REST integration methods
- New: Requests could be not associated to Eloquent models 
- New: Custom request with every Redsys parameter available
- Breaking: Currency amounts handled in cents as integer
- Breaking: Naming for classes, methods and schema changed

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
