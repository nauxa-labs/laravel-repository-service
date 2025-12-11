# Laravel Repository Service

> A flexible Repository and Service pattern implementation for Laravel applications.

![Tests](https://img.shields.io/badge/tests-passing-brightgreen?style=flat-square)
![License](https://img.shields.io/badge/license-MIT-blue?style=flat-square)
![PHP](https://img.shields.io/badge/php-%5E8.1-8892BF?style=flat-square)
![Laravel](https://img.shields.io/badge/laravel-10.x%20%7C%2011.x-FF2D20?style=flat-square)

## ✨ Features

- 🎯 **Flexible Service Pattern** - Define methods with any signature
- 📦 **Standard Repository Pattern** - Complete CRUD operations
- 🚀 **Artisan Commands** - `make:repository` and `make:service`
- 🔍 **Enhanced Query Methods** - `findWhere()`, `paginate()`, `with()`
- ⚡ **Laravel Integration** - Auto-discovery via Service Provider
- ✅ **Fully Tested** - Comprehensive PHPUnit test suite

## Quick Install

```bash
composer require nauxa-labs/laravel-repository-service
```

## Quick Example

```bash
# Generate repository
php artisan make:repository User

# Generate service
php artisan make:service User
```

```php
// Use in your controller
class UserController extends Controller
{
    public function __construct(
        protected UserRepository $userRepository
    ) {}

    public function index()
    {
        return $this->userRepository->paginate(15);
    }

    public function show($id)
    {
        return $this->userRepository->with('posts')->findOrFail($id);
    }
}
```

## Requirements

- PHP ^8.1
- Laravel ^10.0 or ^11.0
