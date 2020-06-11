# Changelog

All notable changes to `soyhuce/dev-tools` will be documented in this file

## [Next release] - YYYY-MM-DD

### Added

- Nothing

### Changed

- Nothing

### Deprecated

- Nothing

### Fixed

- Nothing

### Security

- Nothing

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
