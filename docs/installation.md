# Installation

## Requirements

- PHP ^8.1
- Laravel ^10.0 or ^11.0

## Install via Composer

```bash
composer require nauxa-labs/laravel-repository-service
```

The package will be **auto-discovered** by Laravel. No additional configuration needed.

## Manual Registration (Optional)

If auto-discovery is disabled, add the service provider to `config/app.php`:

```php
'providers' => [
    // ...
    Nauxa\RepositoryService\RepositoryServiceProvider::class,
],
```

## Verify Installation

Run the following command to verify:

```bash
php artisan make:repository --help
```

You should see:

```
Description:
  Create a new repository interface and implementation class

Usage:
  make:repository [options] [--] <name>

Options:
  -m, --model[=MODEL]  The model that the repository applies to
  -f, --force          Create the class even if the repository already exists
```

## Publishing Config (Optional)

Publish the config to customize generator paths:

```bash
php artisan vendor:publish --tag=repository-service-config
```

This creates `config/repository-service.php` where you can customize:

```php
return [
    'paths' => [
        'repositories' => 'Repositories',  // or 'Domain/User/Repositories'
        'services' => 'Services',
        'models' => 'Models',
    ],
    'suffixes' => [
        'interface' => '',
        'implementation' => 'Implement',
    ],
];
```

## Auto-Binding (Optional)

Enable automatic binding of repository and service interfaces:

```php
// config/repository-service.php
'auto_binding' => [
    'enabled' => true,  // Enable auto-binding
    'repositories' => true,
    'services' => true,
],
```

With auto-binding enabled, you don't need to register bindings manually:

```php
// ❌ No longer needed in AppServiceProvider
$this->app->bind(UserRepository::class, UserRepositoryImplement::class);

// ✅ Just inject and use!
public function __construct(UserRepository $userRepository) {}
```

> **Note:** Manual bindings always take priority over auto-bindings.

## Publishing Stubs (Optional)

Customize the generated code by publishing stubs:

```bash
php artisan vendor:publish --tag=repository-service-stubs
```

This creates customizable stub files in your `stubs/` directory.
