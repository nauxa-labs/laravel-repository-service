<?php

declare(strict_types=1);

namespace Nauxa\RepositoryService\Support;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Throwable;

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

    protected string $appNamespace;

    public function __construct(Application $app)
    {
        $this->app = $app;
        $this->appNamespace = $this->getAppNamespace();
    }

    /**
     * Get the application namespace.
     *
     * @return string
     */
    protected function getAppNamespace(): string
    {
        try {
            return $this->app->getNamespace();
        } catch (Throwable) {
            return 'App\\';
        }
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
        // Sanitize path to prevent directory traversal
        $sanitizedPath = $this->sanitizePath($relativePath);
        
        if ($sanitizedPath === null) {
            return;
        }

        $basePath = $this->app->path($sanitizedPath);

        if (!File::isDirectory($basePath)) {
            return;
        }

        $suffix = config('repository-service.suffixes.implementation', 'Implement');
        
        // Use allFiles to scan subdirectories recursively
        $files = File::allFiles($basePath);

        foreach ($files as $file) {
            if ($file->getExtension() !== 'php') {
                continue;
            }

            $className = $file->getFilenameWithoutExtension();

            // Skip implementation classes
            if (str_ends_with($className, $suffix)) {
                continue;
            }

            // Calculate relative path from base for nested directories
            $relativeDir = $file->getRelativePath();
            $namespacePath = $sanitizedPath;
            
            if ($relativeDir !== '') {
                $namespacePath .= '/' . $relativeDir;
            }

            $namespace = rtrim($this->appNamespace, '\\') . '\\' . str_replace('/', '\\', $namespacePath);
            $interface = $namespace . '\\' . $className;
            $implementation = $namespace . '\\' . $className . $suffix;

            $this->tryBind($interface, $implementation);
        }
    }

    /**
     * Sanitize path to prevent directory traversal attacks.
     *
     * @param string $path
     * @return string|null
     */
    protected function sanitizePath(string $path): ?string
    {
        // Remove any directory traversal attempts
        $sanitized = str_replace(['../', '..\\', '..'], '', $path);
        
        // Normalize slashes
        $sanitized = str_replace('\\', '/', $sanitized);
        
        // Remove leading/trailing slashes
        $sanitized = trim($sanitized, '/');
        
        // Validate path doesn't escape app directory
        $fullPath = $this->app->path($sanitized);
        $appPath = $this->app->path();
        
        if (!str_starts_with(realpath($fullPath) ?: $fullPath, realpath($appPath) ?: $appPath)) {
            return null;
        }
        
        return $sanitized;
    }

    /**
     * Attempt to bind interface to implementation with error handling.
     *
     * @param string $interface
     * @param string $implementation
     * @return void
     */
    protected function tryBind(string $interface, string $implementation): void
    {
        try {
            // Only bind if both classes exist and not already bound
            if (
                interface_exists($interface) &&
                class_exists($implementation) &&
                !$this->app->bound($interface)
            ) {
                $this->app->bind($interface, $implementation);
            }
        } catch (Throwable) {
            // Silently ignore binding errors to prevent app crashes
        }
    }
}

