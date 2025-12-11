<?php

declare(strict_types=1);

namespace Nauxa\RepositoryService;

use Illuminate\Support\ServiceProvider;
use Nauxa\RepositoryService\Commands\MakeRepositoryCommand;
use Nauxa\RepositoryService\Commands\MakeServiceCommand;
use Nauxa\RepositoryService\Support\AutoBinder;

/**
 * Repository Service Provider.
 *
 * Handles the registration and bootstrapping of the repository service package.
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
        $this->mergeConfigFrom(
            __DIR__ . '/../config/repository-service.php',
            'repository-service'
        );

        $this->registerAutoBindings();
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
                __DIR__ . '/../config/repository-service.php' => $this->app->configPath('repository-service.php'),
            ], 'repository-service-config');

            $this->publishes([
                __DIR__ . '/../stubs' => $this->app->basePath('stubs'),
            ], 'repository-service-stubs');
        }
    }

    /**
     * Register auto-bindings if enabled.
     *
     * @return void
     */
    protected function registerAutoBindings(): void
    {
        if (! config('repository-service.auto_binding.enabled', false)) {
            return;
        }

        $autoBinder = new AutoBinder($this->app);
        $autoBinder->bindRepositories();
        $autoBinder->bindServices();
    }
}
