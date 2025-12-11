# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

## [2.2.0] - 2024-12-11

### Added
- **Auto-Binding**: Automatically bind repository/service interfaces to implementations
- New `AutoBinder` class in `Support` namespace
- `auto_binding.enabled` config option (off by default for backward compatibility)

### Changed
- `RepositoryServiceProvider` now calls `registerAutoBindings()` when enabled

## [2.1.0] - 2024-12-11

### Added
- **Configuration File**: Publish `repository-service.php` to customize generator paths
- Commands now read paths from config (`repository-service.paths.repositories`, `repository-service.paths.services`)

### Changed
- Updated `RepositoryServiceProvider` to merge and publish config

## [2.0.0] - 2024-12-11

### Added
- **Artisan Commands**: `make:repository` and `make:service` commands for scaffolding
- **Enhanced Repository Methods**:
  - `findWhere(array $conditions)` - Find records by conditions
  - `findWhereIn(string $column, array $values)` - Find records where column is in values
  - `paginate(int $perPage, array $columns)` - Paginate results
  - `firstOrCreate(array $attributes, array $values)` - First or create pattern
  - `with(array|string $relations)` - Eager loading support
  - `resetWith()` - Reset eager loading
- **Testing Infrastructure**: PHPUnit tests with Orchestra Testbench
- **CI/CD**: GitHub Actions workflow for automated testing
- **Documentation**: CHANGELOG.md, CONTRIBUTING.md

### Changed
- `EloquentRepository` now uses `newQuery()` method internally for consistent query building
- Added `$with` property for eager loading state management

## [1.0.0] - 2024-12-11

### Added
- Initial release
- `RepositoryContract` interface with basic CRUD methods
- `EloquentRepository` abstract class implementation
- `ServiceContract` interface (intentionally empty for flexibility)
- `BaseService` abstract class (intentionally empty for flexibility)
- `RepositoryServiceProvider` for Laravel auto-discovery
- MIT License
