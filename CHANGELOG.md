# Changelog

All notable changes to `soyhuce/dev-tools` will be documented in this file

## [Next release] - YYYY-MM-DD

### Added

- CounterCollector
- DebugManager::measuring(string $name, callable $callable)
- DebugManager::dd(...$args)
- Docblocks on Debug facade

### Changed

- Debug does not need anymore to be handled manually outside of an HTTP context. 
- Time measures having same name are consolidated to provide statistics on durations. 

### Deprecated

- Nothing

### Fixed

- Calling the DebugManager or a collector when disabled does not fail anymore

### Security

- Nothing


## [2.4.1] - 2020-05-12

### Fixed

- Fix merging configuration file

## [2.4.0] - 2020-05-11

### Added

- Provides a lot of tools for laravel development
