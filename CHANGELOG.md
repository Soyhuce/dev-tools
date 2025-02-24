# Changelog

All notable changes to `soyhuce/dev-tools` will be documented in this file

## 4.1.0 - 2025-02-24

### What's Changed

* Run tests with Laravel 12 by @bastien-phi in https://github.com/Soyhuce/dev-tools/pull/25

**Full Changelog**: https://github.com/Soyhuce/dev-tools/compare/4.0.0...4.1.0

## 4.0.0 - 2025-01-02

### What's Changed

* Upgrade dependencies and php 8.4 by @bastien-phi in https://github.com/Soyhuce/dev-tools/pull/24

**Full Changelog**: https://github.com/Soyhuce/dev-tools/compare/3.9.0...4.0.0

## 3.9.0 - 2024-03-08

### What's Changed

* Laravel 11 support by @EdenMl in https://github.com/Soyhuce/dev-tools/pull/23

**Full Changelog**: https://github.com/Soyhuce/dev-tools/compare/3.8.0...3.9.0

## [3.3.2] - 2022-03-18

### Fixed

- Fixed ColorUtils::getComplementaryColor for white (`[255,255,255]`)

## [3.3.1] - 2022-01-07

### Added

- Support Laravel 9

## [3.3.0] - 2021-12-08

### Added

- Phpstan analysis
- Php 8.1 support

### Changed

- Drop Laravel 7 support
- Drop php 7.4 support
- Upgrade dependencies
- Use php 8.0 syntax
- Improve Query collector log

## [3.2.0] - 2020-11-09

### Added

- Add PHP8 Support

## [3.1.0] - 2020-08-27

### Changed

- Drop Laravel 6 support
- Add Laravel 8 support

## [3.0.1] - 2020-06-25

### Fixed

- Fix issue when following redirect into tests (bc191b4cd8fc44b0885e48807e65107a87a93d2b)

## [3.0.0] - 2020-06-11

### Added

- CounterCollector
- DebugManager::measuring(string $name, callable $callable)
- DebugManager::dd(...$args)
- Docblocks on Debug facade
- ArtisanCollector

### Changed

- Debug does not need anymore to be handled manually outside of an HTTP context.
- Time measures having same name are consolidated to provide statistics on durations.

### Fixed

- Calling the DebugManager or a collector when disabled does not fail anymore

## [2.4.1] - 2020-05-12

### Fixed

- Fix merging configuration file

## [2.4.0] - 2020-05-11

### Added

- Provides a lot of tools for laravel development
