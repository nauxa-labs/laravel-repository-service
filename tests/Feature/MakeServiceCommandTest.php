<?php

declare(strict_types=1);

namespace Nauxa\RepositoryService\Tests\Feature;

use Illuminate\Support\Facades\File;
use Nauxa\RepositoryService\Tests\TestCase;

class MakeServiceCommandTest extends TestCase
{
    protected function tearDown(): void
    {
        // Clean up generated test files
        $appPath = $this->app->basePath('app');
        $paths = [
            $appPath . '/Services',
            $appPath . '/Domain',
        ];

        foreach ($paths as $path) {
            if (File::isDirectory($path)) {
                File::deleteDirectory($path);
            }
        }

        parent::tearDown();
    }

    /** @test */
    public function testMakeServiceCreatesInterfaceAndImplementation(): void
    {
        $this->artisan('make:service', ['name' => 'User'])
            ->assertSuccessful()
            ->expectsOutputToContain('Service interface [User] created successfully')
            ->expectsOutputToContain('Service implementation [UserImplement] created successfully');
    }

    /** @test */
    public function testMakeServiceWithForceOption(): void
    {
        // Create first time
        $this->artisan('make:service', ['name' => 'Order'])
            ->assertSuccessful();

        // Create again with --force should succeed
        $this->artisan('make:service', [
            'name' => 'Order',
            '--force' => true,
        ])->assertSuccessful();
    }

    /** @test */
    public function testMakeServiceWithCustomConfigPath(): void
    {
        // Configure custom path
        config(['repository-service.paths.services' => 'Domain/Order/Services']);

        $this->artisan('make:service', ['name' => 'Payment'])
            ->assertSuccessful();
    }

    /** @test */
    public function testMakeServiceShowsErrorWhenAlreadyExists(): void
    {
        // Create first time
        $this->artisan('make:service', ['name' => 'Existing'])
            ->assertSuccessful();

        // Try to create again without --force shows error
        $this->artisan('make:service', ['name' => 'Existing'])
            ->assertSuccessful()
            ->expectsOutputToContain('already exists');
    }
}
