# Service API Reference

API reference for `BaseService` and `ServiceContract`.

## Overview

Unlike the Repository pattern with predefined CRUD methods, the Service pattern in this package is **intentionally flexible**. Both `ServiceContract` and `BaseService` are empty by design.

This allows you to:
- Define any method signature
- Use any parameter types
- Return any types
- Implement business logic without constraints

## ServiceContract

The base service interface.

```php
<?php

namespace Refinaldy\RepositoryService\Contracts;

interface ServiceContract
{
    // Empty - define your own methods
}
```

## BaseService

The abstract base service class.

```php
<?php

namespace Refinaldy\RepositoryService\Abstracts;

abstract class BaseService implements ServiceContract
{
    // Empty - implement your own logic
}
```

## Example Usage

### User Service

```php
interface UserService extends ServiceContract
{
    public function register(RegisterDTO $dto): User;
    public function login(LoginDTO $dto): ?string;
    public function logout(User $user): void;
    public function updatePassword(User $user, string $newPassword): bool;
}
```

### Order Service

```php
interface OrderService extends ServiceContract
{
    public function create(CreateOrderDTO $dto): Order;
    public function cancel(Order $order, string $reason): void;
    public function refund(Order $order): RefundResult;
    public function calculateTotal(array $items): Money;
}
```

### Payment Service

```php
interface PaymentService extends ServiceContract
{
    public function charge(PaymentIntent $intent): PaymentResult;
    public function createSubscription(User $user, Plan $plan): Subscription;
    public function handleWebhook(array $payload): void;
}
```

## Best Practices

### 1. Keep Services Focused

```php
// ❌ Too many responsibilities
interface UserService {
    public function register();
    public function sendEmail();
    public function processPayment();
    public function generateReport();
}

// ✅ Single responsibility
interface AuthService {
    public function register();
    public function login();
}

interface EmailService {
    public function send();
}

interface PaymentService {
    public function charge();
}
```

### 2. Use DTOs for Complex Input

```php
// ❌ Too many parameters
public function createOrder(
    int $userId,
    array $items,
    string $shippingAddress,
    string $billingAddress,
    ?string $couponCode,
    string $paymentMethod
): Order;

// ✅ Use DTO
public function createOrder(CreateOrderDTO $dto): Order;
```

### 3. Return Meaningful Types

```php
// ❌ Vague return type
public function process(): mixed;

// ✅ Specific return type
public function process(): ProcessResult;
```
