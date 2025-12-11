# Quick Start

Get up and running in 5 minutes!

## Step 1: Install Package

```bash
composer require nauxa-labs/laravel-repository-service
```

## Step 2: Generate Repository

```bash
php artisan make:repository User
```

This creates two files:

**`app/Repositories/UserRepository.php`** (Interface)
```php
<?php

namespace App\Repositories;

use Nauxa\RepositoryService\Contracts\RepositoryContract;

interface UserRepository extends RepositoryContract
{
    //
}
```

**`app/Repositories/UserRepositoryImplement.php`** (Implementation)
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
}
```

## Step 3: Register Binding

Add to `AppServiceProvider.php`:

```php
public function register(): void
{
    $this->app->bind(
        \App\Repositories\UserRepository::class,
        \App\Repositories\UserRepositoryImplement::class
    );
}
```

## Step 4: Use in Controller

```php
<?php

namespace App\Http\Controllers;

use App\Repositories\UserRepository;

class UserController extends Controller
{
    public function __construct(
        protected UserRepository $userRepository
    ) {}

    public function index()
    {
        return $this->userRepository->paginate(15);
    }

    public function show(int $id)
    {
        return $this->userRepository->findOrFail($id);
    }

    public function store(Request $request)
    {
        return $this->userRepository->create($request->validated());
    }

    public function update(Request $request, int $id)
    {
        $this->userRepository->update($id, $request->validated());
        return response()->noContent();
    }

    public function destroy(int $id)
    {
        $this->userRepository->delete($id);
        return response()->noContent();
    }
}
```

## Next Steps

- Learn about [Repository Methods](api/repository.md)
- Generate a [Service Layer](guide/service.md)
- Explore [Artisan Commands](guide/commands.md)
