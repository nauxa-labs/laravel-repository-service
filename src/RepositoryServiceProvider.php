<?php

declare(strict_types=1);

namespace Refinaldy\RepositoryService;

use Illuminate\Support\ServiceProvider;

/**
 * Repository Service Provider
 *
 * Handles the registration and bootstrapping of the repository service package.
 *
 * @package Refinaldy\RepositoryService
 */
class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register(): void
    {
        // Package registration logic can be added here
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(): void
    {
        // Package bootstrapping logic can be added here
    }
}
