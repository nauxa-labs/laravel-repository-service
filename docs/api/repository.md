# Repository API Reference

Complete API reference for `EloquentRepository` methods.

## CRUD Methods

### find

Find a record by primary key.

```php
public function find(int|string $id): ?Model
```

**Example:**
```php
$user = $this->userRepository->find(1);

if ($user) {
    echo $user->name;
}
```

---

### findOrFail

Find a record by primary key or throw `ModelNotFoundException`.

```php
public function findOrFail(int|string $id): Model
```

**Example:**
```php
try {
    $user = $this->userRepository->findOrFail(999);
} catch (ModelNotFoundException $e) {
    abort(404);
}
```

---

### all

Get all records.

```php
public function all(): Collection
```

**Example:**
```php
$users = $this->userRepository->all();
```

---

### create

Create a new record.

```php
public function create(array $attributes): Model
```

**Example:**
```php
$user = $this->userRepository->create([
    'name' => 'John Doe',
    'email' => 'john@example.com',
]);
```

---

### update

Update a record by primary key.

```php
public function update(int|string $id, array $attributes): bool
```

**Example:**
```php
$success = $this->userRepository->update(1, [
    'name' => 'Jane Doe',
]);
```

---

### delete

Delete a record by primary key.

```php
public function delete(int|string $id): bool
```

**Example:**
```php
$deleted = $this->userRepository->delete(1);
```

---

### destroy

Delete multiple records by primary keys.

```php
public function destroy(array $ids): int
```

**Returns:** Number of deleted records.

**Example:**
```php
$count = $this->userRepository->destroy([1, 2, 3]);
echo "Deleted {$count} records";
```

---

## Query Methods

### findWhere

Find records matching conditions.

```php
public function findWhere(array $conditions): Collection
```

**Example:**
```php
$admins = $this->userRepository->findWhere([
    'role' => 'admin',
    'status' => 'active',
]);
```

---

### findWhereIn

Find records where column value is in array.

```php
public function findWhereIn(string $column, array $values): Collection
```

**Example:**
```php
$users = $this->userRepository->findWhereIn('id', [1, 2, 3, 4, 5]);
```

---

### paginate

Paginate results.

```php
public function paginate(int $perPage = 15, array $columns = ['*']): LengthAwarePaginator
```

**Example:**
```php
$users = $this->userRepository->paginate(20);

// In blade
@foreach($users as $user)
    {{ $user->name }}
@endforeach

{{ $users->links() }}
```

---

### firstOrCreate

Find first matching record or create it.

```php
public function firstOrCreate(array $attributes, array $values = []): Model
```

**Example:**
```php
$user = $this->userRepository->firstOrCreate(
    ['email' => 'john@example.com'],       // Search attributes
    ['name' => 'John Doe', 'role' => 'user'] // Create values
);
```

---

## Relationship Methods

### with

Set relationships to eager load.

```php
public function with(array|string $relations): static
```

**Example:**
```php
// Single relation
$users = $this->userRepository->with('posts')->all();

// Multiple relations  
$users = $this->userRepository->with(['posts', 'comments'])->paginate(10);

// Chaining
$user = $this->userRepository
    ->with('posts.comments')
    ->findOrFail(1);
```

---

### resetWith

Reset eager loading.

```php
public function resetWith(): static
```

**Example:**
```php
$this->userRepository->resetWith();
```

---

## Model Methods

### getModel

Get the underlying Eloquent model.

```php
public function getModel(): Model
```

**Example:**
```php
$model = $this->userRepository->getModel();
echo $model->getTable(); // 'users'
```

---

### setModel

Set a new model instance.

```php
public function setModel(Model $model): static
```

**Example:**
```php
$newModel = new User();
$this->userRepository->setModel($newModel);
```
