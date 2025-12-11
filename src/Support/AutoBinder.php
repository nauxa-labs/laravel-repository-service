<?php

declare(strict_types=1);

namespace Nauxa\RepositoryService\Support;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\Facades\File;
use ReflectionClass;

/**
 * Auto-Binder Support Class
 *
 * Automatically binds repository and service interfaces to their implementations.
 *
 * @package Nauxa\RepositoryService
 */
class AutoBinder
{
    protected Application $app;

    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    /**
     * Bind all repositories automatically.
     *
     * @return void
     */
    public function bindRepositories(): void
    {
        if (!config('repository-service.auto_binding.repositories', true)) {
            return;
        }

        $path = config('repository-service.paths.repositories', 'Repositories');
        $this->bindFromPath($path);
    }

    /**
     * Bind all services automatically.
     *
     * @return void
     */
    public function bindServices(): void
    {
        if (!config('repository-service.auto_binding.services', true)) {
            return;
        }

        $path = config('repository-service.paths.services', 'Services');
        $this->bindFromPath($path);
    }

    /**
     * Bind interfaces to implementations from a given path.
     *
     * @param string $relativePath
     * @return void
     */
    protected function bindFromPath(string $relativePath): void
    {
        $basePath = $this->app->path(str_replace('\\', '/', $relativePath));

        if (!File::isDirectory($basePath)) {
            return;
        }

        $suffix = config('repository-service.suffixes.implementation', 'Implement');
        $files = File::files($basePath);

        foreach ($files as $file) {
            if ($file->getExtension() !== 'php') {
                continue;
            }

            $className = $file->getFilenameWithoutExtension();

            // Skip implementation classes
            if (str_ends_with($className, $suffix)) {
                continue;
            }

            $namespace = 'App\\' . str_replace('/', '\\', $relativePath);
            $interface = $namespace . '\\' . $className;
            $implementation = $namespace . '\\' . $className . $suffix;

            // Only bind if both classes exist and not already bound
            if (
                interface_exists($interface) &&
                class_exists($implementation) &&
                !$this->app->bound($interface)
            ) {
                $this->app->bind($interface, $implementation);
            }
        }
    }
}
