<?php

declare(strict_types=1);

namespace Nauxa\RepositoryService;

use Illuminate\Support\ServiceProvider;
use Nauxa\RepositoryService\Commands\MakeRepositoryCommand;
use Nauxa\RepositoryService\Commands\MakeServiceCommand;

/**
 * Repository Service Provider
 *
 * Handles the registration and bootstrapping of the repository service package.
 *
 * @package Nauxa\RepositoryService
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
        if ($this->app->runningInConsole()) {
            $this->commands([
                MakeRepositoryCommand::class,
                MakeServiceCommand::class,
            ]);

            $this->publishes([
                __DIR__ . '/../stubs' => $this->app->basePath('stubs'),
            ], 'repository-service-stubs');
        }
    }
}
