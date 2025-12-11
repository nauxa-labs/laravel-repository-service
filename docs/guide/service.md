# Service Guide

The Service pattern provides a layer for business logic, keeping controllers thin and focused.

## Creating a Service

### Using Artisan Command

```bash
php artisan make:service User
```

### Manual Creation

**Interface:**
```php
<?php

namespace App\Services;

use Nauxa\RepositoryService\Contracts\ServiceContract;

interface UserService extends ServiceContract
{
    public function register(UserDTO $dto): User;
    public function updateProfile(int $userId, array $data): bool;
    public function deactivateAccount(int $userId): void;
}
```

**Implementation:**
```php
<?php

namespace App\Services;

use App\Repositories\UserRepository;
use Nauxa\RepositoryService\Abstracts\BaseService;

class UserServiceImplement extends BaseService implements UserService
{
    public function __construct(
        protected UserRepository $userRepository
    ) {}

    public function register(UserDTO $dto): User
    {
        // Hash password
        $data = $dto->toArray();
        $data['password'] = bcrypt($data['password']);

        // Create user
        $user = $this->userRepository->create($data);

        // Send welcome email
        Mail::to($user)->send(new WelcomeEmail($user));

        return $user;
    }

    public function updateProfile(int $userId, array $data): bool
    {
        return $this->userRepository->update($userId, $data);
    }

    public function deactivateAccount(int $userId): void
    {
        $this->userRepository->update($userId, ['status' => 'inactive']);
        
        // Additional cleanup logic
        event(new UserDeactivated($userId));
    }
}
```

## Why Use Services?

### ❌ Without Service (Fat Controller)
```php
public function register(Request $request)
{
    $data = $request->validated();
    $data['password'] = bcrypt($data['password']);
    
    $user = User::create($data);
    
    Mail::to($user)->send(new WelcomeEmail($user));
    event(new UserRegistered($user));
    
    return $user;
}
```

### ✅ With Service (Thin Controller)
```php
public function register(Request $request)
{
    return $this->userService->register(
        UserDTO::from($request->validated())
    );
}
```

## Combining with Repository

```php
class OrderServiceImplement extends BaseService implements OrderService
{
    public function __construct(
        protected OrderRepository $orderRepository,
        protected ProductRepository $productRepository,
        protected UserRepository $userRepository
    ) {}

    public function placeOrder(int $userId, array $items): Order
    {
        $user = $this->userRepository->findOrFail($userId);

        // Calculate totals
        $total = collect($items)->sum(function ($item) {
            $product = $this->productRepository->findOrFail($item['product_id']);
            return $product->price * $item['quantity'];
        });

        // Create order
        return $this->orderRepository->create([
            'user_id' => $userId,
            'total' => $total,
            'items' => $items,
        ]);
    }
}
```

## Flexibility

`BaseService` is intentionally empty - you define all method signatures yourself:

```php
// ✅ Any return type
public function getStats(): array;
public function calculateDiscount(Order $order): float;
public function sendNotification(): void;

// ✅ Any parameters
public function processPayment(PaymentDTO $dto, ?Coupon $coupon = null): bool;
```
