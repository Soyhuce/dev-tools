# Changelog

All notable changes to `soyhuce/dev-tools` will be documented in this file

## [Next release] - YYYY-MM-DD

### Added

- Phpstan analysis

### Changed

- Drop Laravel 7 support
- Drop php 7.4 support
- Upgrade dependencies
- Use php 8.0 syntax

### Deprecated

- Nothing

### Fixed

- Nothing

### Security

- Nothing

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
