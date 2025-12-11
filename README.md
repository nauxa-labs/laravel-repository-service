# Laravel Repository Service

A flexible Repository and Service pattern implementation for Laravel applications.

## Features

- **Flexible Service Pattern**: `ServiceContract` and `BaseService` are intentionally empty, allowing you to define methods with any signature
- **Standard Repository Pattern**: `RepositoryContract` and `EloquentRepository` provide standard CRUD operations
- **Laravel Integration**: Auto-discovery support via Service Provider

## Requirements

- PHP ^8.1
- Laravel ^10.0 or ^11.0

## Installation

### Option 1: Install from GitHub (Private Repository)

Add the repository to your `composer.json`:

```json
{
    "repositories": [
        {
            "type": "vcs",
            "url": "https://github.com/refinaldy/laravel-repository-service"
        }
    ],
    "require": {
        "refinaldy/laravel-repository-service": "dev-main"
    }
}
```

Then run:

```bash
composer update refinaldy/laravel-repository-service
```

### Option 2: Install from Local Path

If the package is in a local folder:

```json
{
    "repositories": [
        {
            "type": "path",
            "url": "./packages/refinaldy/laravel-repository-service"
        }
    ],
    "require": {
        "refinaldy/laravel-repository-service": "*"
    }
}
```

## Usage

### Creating a Service

```php
<?php

namespace App\Services\User;

use Refinaldy\RepositoryService\Contracts\ServiceContract;

interface UserService extends ServiceContract
{
    // Define your own methods with any signature
    public function create(UserDTO $dto, ?string $role = null): User;
    public function findByEmail(string $email): ?User;
}
```

```php
<?php

namespace App\Services\User;

use Refinaldy\RepositoryService\Abstracts\BaseService;

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

namespace App\Repositories\User;

use Refinaldy\RepositoryService\Contracts\RepositoryContract;

interface UserRepository extends RepositoryContract
{
    // Add custom methods if needed
    public function findByEmail(string $email): ?User;
}
```

```php
<?php

namespace App\Repositories\User;

use App\Models\User;
use Refinaldy\RepositoryService\Abstracts\EloquentRepository;

class UserRepositoryImplement extends EloquentRepository implements UserRepository
{
    public function __construct(User $model)
    {
        $this->model = $model;
    }

    public function findByEmail(string $email): ?User
    {
        return $this->model->where('email', $email)->first();
    }
}
```

## Available Methods

### EloquentRepository (CRUD)

| Method | Description |
|--------|-------------|
| `find($id)` | Find a record by ID |
| `findOrFail($id)` | Find a record by ID or throw exception |
| `all()` | Get all records |
| `create(array $attributes)` | Create a new record |
| `update($id, array $attributes)` | Update a record |
| `delete($id)` | Delete a record |
| `destroy(array $ids)` | Delete multiple records |

### BaseService

Empty by design - define your own methods!

## License

MIT License - see [LICENSE](LICENSE) file.
