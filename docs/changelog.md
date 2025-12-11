# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

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
- **Documentation**: Full documentation site with Docsify

### Changed
- `EloquentRepository` now uses `newQuery()` method internally for consistent query building

## [1.0.0] - 2024-12-11

### Added
- Initial release
- `RepositoryContract` interface with basic CRUD methods
- `EloquentRepository` abstract class implementation
- `ServiceContract` interface (intentionally empty for flexibility)
- `BaseService` abstract class (intentionally empty for flexibility)
- `RepositoryServiceProvider` for Laravel auto-discovery
