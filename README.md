# Laravel Repository Service

[![Tests](https://github.com/nauxa-labs/laravel-repository-service/actions/workflows/tests.yml/badge.svg)](https://github.com/nauxa-labs/laravel-repository-service/actions/workflows/tests.yml)
[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg)](https://opensource.org/licenses/MIT)
[![PHP Version](https://img.shields.io/badge/php-%5E8.1-8892BF.svg)](https://www.php.net)
[![Laravel Version](https://img.shields.io/badge/laravel-10.x%20%7C%2011.x-FF2D20.svg)](https://laravel.com)

A flexible Repository and Service pattern implementation for Laravel applications.

## ✨ Features

- 🎯 **Flexible Service Pattern**: `ServiceContract` and `BaseService` are intentionally empty, allowing you to define methods with any signature
- 📦 **Standard Repository Pattern**: `RepositoryContract` and `EloquentRepository` provide standard CRUD operations
- 🚀 **Artisan Commands**: Generate repositories and services with `make:repository` and `make:service`
- 🔍 **Enhanced Query Methods**: `findWhere()`, `findWhereIn()`, `paginate()`, `with()`, `firstOrCreate()`
- ⚡ **Laravel Integration**: Auto-discovery support via Service Provider
- ✅ **Fully Tested**: Comprehensive test suite with PHPUnit

## Requirements

- PHP ^8.1
- Laravel ^10.0 or ^11.0

## Installation

```bash
composer require nauxa-labs/laravel-repository-service
```

The package will be auto-discovered by Laravel. No additional configuration needed.

## Quick Start

### Generate a Repository

```bash
php artisan make:repository User
```

This creates:
- `app/Repositories/UserRepository.php` (interface)
- `app/Repositories/UserRepositoryImplement.php` (implementation)

### Generate a Service

```bash
php artisan make:service User
```

This creates:
- `app/Services/UserService.php` (interface)
- `app/Services/UserServiceImplement.php` (implementation)

## Usage

### Creating a Service

```php
<?php

namespace App\Services;

use Nauxa\RepositoryService\Contracts\ServiceContract;

interface UserService extends ServiceContract
{
    // Define your own methods with any signature
    public function create(UserDTO $dto, ?string $role = null): User;
    public function findByEmail(string $email): ?User;
}
```

```php
<?php

namespace App\Services;

use Nauxa\RepositoryService\Abstracts\BaseService;

class UserServiceImplement extends BaseService implements UserService
{
    public function __construct(
        protected UserRepository $userRepository
    ) {}

    public function create(UserDTO $dto, ?string $role = null): User
    {
        // Your implementation
    }

    public function findByEmail(string $email): ?User
    {
        // Your implementation
    }
}
```

### Creating a Repository

```php
<?php

namespace App\Repositories;

use Nauxa\RepositoryService\Contracts\RepositoryContract;

interface UserRepository extends RepositoryContract
{
    // Add custom methods if needed
    public function findByEmail(string $email): ?User;
}
```

```php
<?php

namespace App\Repositories;

use App\Models\User;
use Nauxa\RepositoryService\Abstracts\EloquentRepository;

class UserRepositoryImplement extends EloquentRepository implements UserRepository
{
    public function __construct(User $model)
    {
        $this->model = $model;
    }

    public function findByEmail(string $email): ?User
    {
        return $this->findWhere(['email' => $email])->first();
    }
}
```

## Available Methods

### EloquentRepository

| Method | Description |
|--------|-------------|
| `find($id)` | Find a record by ID |
| `findOrFail($id)` | Find a record by ID or throw exception |
| `findWhere(array $conditions)` | Find records matching conditions |
| `findWhereIn(string $column, array $values)` | Find records where column is in values |
| `all()` | Get all records |
| `paginate(int $perPage = 15)` | Paginate results |
| `create(array $attributes)` | Create a new record |
| `firstOrCreate(array $attributes, array $values = [])` | First or create pattern |
| `update($id, array $attributes)` | Update a record |
| `delete($id)` | Delete a record |
| `destroy(array $ids)` | Delete multiple records |
| `with(array\|string $relations)` | Set eager loading relations |

### Eager Loading Example

```php
// Load users with their posts
$users = $this->userRepository->with(['posts', 'profile'])->all();

// Or chain with other methods
$users = $this->userRepository->with('posts')->paginate(10);
```

### BaseService

Empty by design - define your own methods!

## Publishing Stubs

You can publish the stubs to customize the generated code:

```bash
php artisan vendor:publish --tag=repository-service-stubs
```

## Testing

```bash
composer test
```

## Contributing

Please see [CONTRIBUTING.md](CONTRIBUTING.md) for details.

## Changelog

Please see [CHANGELOG.md](CHANGELOG.md) for a list of changes.

## License

MIT License - see [LICENSE](LICENSE) file.
