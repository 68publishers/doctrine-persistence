# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

## [0.4.0] - 2026-10-28
### Added
- Support for `doctrine/orm` v3 (and DBAL v4).
- Suggested packages `nette/application` (for `RedirectTransactionExtender`) and `psr/log` (for `ErrorLogTransactionExtender`).
- GitHub Actions workflows (coding style, tests on PHP 8.3, 8.4 and 8.5 with highest dependencies and on PHP 8.3 with lowest dependencies).

### Changed
- Minimal PHP version is 8.3.
- Minimal `doctrine/orm` version is 2.8 (older versions cannot be installed on PHP 8).
- The `$previous` parameter of the persistence exceptions is explicitly nullable (`?Throwable`), PHP 8.4 no longer emits the implicit nullable deprecation.
- Updated dev dependencies (`friendsofphp/php-cs-fixer` v3, `mockery/mockery` ^1.6.12, `nette/tester` ^2.6, `nette/*`).

### Removed
- Support for PHP < 8.3.
- Travis CI configuration.

### Fixed
- Error callbacks with an untyped, `mixed`, `object`, union or intersection typed exception parameter. Previously, an untyped parameter caused an `Error` ("Class name must be a valid object or a string") that replaced the original exception, and union/intersection types were never invoked.
- `EntityNotFoundException` with a composite (array) identifier no longer emits "Array to string conversion"; the identifier is rendered as JSON in the message.
- `EntityNotFoundException` keeps a composite identifier with invalid UTF-8 in the message (the invalid bytes are replaced), previously the identifier was missing.

## [0.3.1] - 2024-08-06
### Changed
- Allowed `nette/utils` v4.

## [0.3.0] - 2023-01-03
### Added
- PHP 8.1 support.

### Changed
- Added the support warning into the README.

## [0.2.7] - 2021-10-26
### Added
- Transaction ID and the remove method for the transaction context.
- Method `TransactionTrackerInterface::getTransaction()`.

### Changed
- Updated the Php-Cs-Fixer and Travis CI configuration.

## [0.2.6] - 2021-08-13
### Added
- Method `BadgeBagInterface::add()`.

### Changed
- Nested transactions share one `BadgeBag`.

## [0.2.5] - 2021-04-15
### Fixed
- Default behaviour (throwing) of exceptions that are caught in a transaction.

## [0.2.4] - 2021-04-15
### Fixed
- The last error in finally contexts.

## [0.2.3] - 2021-04-08
### Added
- Transaction tracker.

## [0.2.2] - 2021-02-15
### Added
- Argument `$reason` for the `InvalidValueException` constructor and methods `::setEntity()`, `::getReason()` and `::create()`.

## [0.2.1] - 2021-02-15
### Changed
- Improved persistence exceptions.

## [0.2] - 2020-11-22
Not compatible with the previous version!

### Added
- Support for Nette 3 (Nette 2.4 is still supported).

### Changed
- Improved Transaction objects.

### Removed
- Persistence layer (DTOs, Assemblers, Persisters).

## [0.1.2] - 2019-08-22
### Added
- Custom persistence exceptions.
- Identifier classes for Assemblers.

### Changed
- Improved transaction lifecycle - exceptions catching, possible redirecting on success etc.

## [0.1.1] - 2019-08-07
### Added
- ManyToOne assemblers and persisters.

## [0.1] - 2019-06-17
First release.

- Nette `~2.4`
- Doctrine `^2.5`

[Unreleased]: https://github.com/68publishers/doctrine-persistence/compare/v0.4.0...HEAD
[0.4.0]: https://github.com/68publishers/doctrine-persistence/compare/v0.3.1...v0.4.0
[0.3.1]: https://github.com/68publishers/doctrine-persistence/compare/v0.3.0...v0.3.1
[0.3.0]: https://github.com/68publishers/doctrine-persistence/compare/v0.2.7...v0.3.0
[0.2.7]: https://github.com/68publishers/doctrine-persistence/compare/v0.2.6...v0.2.7
[0.2.6]: https://github.com/68publishers/doctrine-persistence/compare/v0.2.5...v0.2.6
[0.2.5]: https://github.com/68publishers/doctrine-persistence/compare/v0.2.4...v0.2.5
[0.2.4]: https://github.com/68publishers/doctrine-persistence/compare/v0.2.3...v0.2.4
[0.2.3]: https://github.com/68publishers/doctrine-persistence/compare/v0.2.2...v0.2.3
[0.2.2]: https://github.com/68publishers/doctrine-persistence/compare/v0.2.1...v0.2.2
[0.2.1]: https://github.com/68publishers/doctrine-persistence/compare/v0.2...v0.2.1
[0.2]: https://github.com/68publishers/doctrine-persistence/compare/v0.1.2...v0.2
[0.1.2]: https://github.com/68publishers/doctrine-persistence/compare/v0.1.1...v0.1.2
[0.1.1]: https://github.com/68publishers/doctrine-persistence/compare/v0.1...v0.1.1
[0.1]: https://github.com/68publishers/doctrine-persistence/releases/tag/v0.1
