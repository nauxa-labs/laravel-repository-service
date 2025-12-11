# Artisan Commands

This package provides two artisan commands for generating repository and service classes.

## make:repository

Generate a repository interface and implementation.

### Usage

```bash
php artisan make:repository <name> [options]
```

### Arguments

| Argument | Description |
|----------|-------------|
| `name` | The name of the repository (e.g., `User`, `Post`) |

### Options

| Option | Description |
|--------|-------------|
| `-m, --model=MODEL` | The model class to use |
| `-f, --force` | Overwrite existing files |

### Examples

```bash
# Basic usage
php artisan make:repository User

# With specific model
php artisan make:repository UserRepository --model=User

# Force overwrite
php artisan make:repository User --force
```

### Generated Files

```
app/Repositories/
├── UserRepository.php          # Interface
└── UserRepositoryImplement.php # Implementation
```

---

## make:service

Generate a service interface and implementation.

### Usage

```bash
php artisan make:service <name> [options]
```

### Arguments

| Argument | Description |
|----------|-------------|
| `name` | The name of the service (e.g., `User`, `Order`) |

### Options

| Option | Description |
|--------|-------------|
| `-f, --force` | Overwrite existing files |

### Examples

```bash
# Basic usage
php artisan make:service User

# Force overwrite
php artisan make:service User --force
```

### Generated Files

```
app/Services/
├── UserService.php          # Interface
└── UserServiceImplement.php # Implementation
```

---

## Customizing Stubs

Publish stubs to customize generated code:

```bash
php artisan vendor:publish --tag=repository-service-stubs
```

This creates:

```
stubs/
├── repository.interface.stub
├── repository.implement.stub
├── service.interface.stub
└── service.implement.stub
```

### Example: Custom Repository Stub

`stubs/repository.implement.stub`:
```php
<?php

namespace {{ namespace }};

use App\Models\{{ model }};
use Nauxa\RepositoryService\Abstracts\EloquentRepository;

class {{ class }} extends EloquentRepository implements {{ interfaceName }}
{
    public function __construct({{ model }} $model)
    {
        $this->model = $model;
    }

    // Add your custom methods here
}
```
