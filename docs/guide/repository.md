# Repository Guide

The Repository pattern provides a clean abstraction layer between your application and data access logic.

## Creating a Repository

### Using Artisan Command

```bash
php artisan make:repository User
```

With model option:
```bash
php artisan make:repository User --model=User
```

### Manual Creation

**Interface:**
```php
<?php

namespace App\Repositories;

use Nauxa\RepositoryService\Contracts\RepositoryContract;

interface UserRepository extends RepositoryContract
{
    public function findByEmail(string $email): ?User;
    public function getActiveUsers(): Collection;
}
```

**Implementation:**
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

    public function getActiveUsers(): Collection
    {
        return $this->findWhere(['status' => 'active']);
    }
}
```

## Eager Loading

Use `with()` to eager load relationships:

```php
// Single relation
$users = $this->userRepository->with('posts')->all();

// Multiple relations
$users = $this->userRepository->with(['posts', 'comments'])->paginate(10);

// Nested relations
$users = $this->userRepository->with('posts.comments')->findOrFail($id);
```

Reset eager loading:
```php
$this->userRepository->resetWith();
```

## Query Examples

```php
// Find by conditions
$activeAdmins = $this->userRepository->findWhere([
    'status' => 'active',
    'role' => 'admin'
]);

// Find where in
$users = $this->userRepository->findWhereIn('id', [1, 2, 3]);

// First or create
$user = $this->userRepository->firstOrCreate(
    ['email' => 'john@example.com'],
    ['name' => 'John Doe']
);

// Pagination
$users = $this->userRepository->paginate(15);
```

## Dependency Injection

Register bindings in `AppServiceProvider`:

```php
public function register(): void
{
    $this->app->bind(UserRepository::class, UserRepositoryImplement::class);
    $this->app->bind(PostRepository::class, PostRepositoryImplement::class);
}
```

Inject in controllers:

```php
public function __construct(
    protected UserRepository $userRepository,
    protected PostRepository $postRepository
) {}
```
