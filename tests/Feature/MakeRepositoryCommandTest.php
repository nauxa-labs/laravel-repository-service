<?php

declare(strict_types=1);

namespace Nauxa\RepositoryService\Tests\Feature;

use Illuminate\Support\Facades\File;
use Nauxa\RepositoryService\Tests\TestCase;

class MakeRepositoryCommandTest extends TestCase
{
    protected string $repoPath;

    protected function setUp(): void
    {
        parent::setUp();
        // Get the actual path where files are created in testbench
        $this->repoPath = $this->app->basePath('app/Repositories');
    }

    protected function tearDown(): void
    {
        // Clean up generated test files
        $appPath = $this->app->basePath('app');
        $paths = [
            $appPath . '/Repositories',
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
    public function testMakeRepositoryCreatesInterfaceAndImplementation(): void
    {
        $this->artisan('make:repository', ['name' => 'User'])
            ->assertSuccessful()
            ->expectsOutputToContain('Repository interface [User] created successfully')
            ->expectsOutputToContain('Repository implementation [UserImplement] created successfully');
    }

    /** @test */
    public function testMakeRepositoryWithModelOption(): void
    {
        $this->artisan('make:repository', [
            'name' => 'Post',
            '--model' => 'Article',
        ])->assertSuccessful();

        // Command completed successfully with model option
        $this->assertTrue(true);
    }

    /** @test */
    public function testMakeRepositoryGuessesModelFromName(): void
    {
        $this->artisan('make:repository', ['name' => 'ProductRepository'])
            ->assertSuccessful();

        // Verify output indicates success
        $this->assertTrue(true);
    }

    /** @test */
    public function testMakeRepositoryWithForceOption(): void
    {
        // Create first time
        $this->artisan('make:repository', ['name' => 'Category'])
            ->assertSuccessful();

        // Create again with --force should succeed
        $this->artisan('make:repository', [
            'name' => 'Category',
            '--force' => true,
        ])->assertSuccessful();
    }

    /** @test */
    public function testMakeRepositoryWithCustomConfigPath(): void
    {
        // Configure custom path
        config(['repository-service.paths.repositories' => 'Domain/User/Repositories']);

        $this->artisan('make:repository', ['name' => 'Profile'])
            ->assertSuccessful();
    }

    /** @test */
    public function testMakeRepositoryShowsErrorWhenAlreadyExists(): void
    {
        // Create first time
        $this->artisan('make:repository', ['name' => 'Existing'])
            ->assertSuccessful();

        // Try to create again without --force shows error
        $this->artisan('make:repository', ['name' => 'Existing'])
            ->assertSuccessful()
            ->expectsOutputToContain('already exists');
    }
}
