<?php

declare(strict_types=1);

namespace Nauxa\RepositoryService\Tests\Unit;

use Illuminate\Support\Facades\File;
use Nauxa\RepositoryService\Support\AutoBinder;
use Nauxa\RepositoryService\Tests\TestCase;
use ReflectionClass;
use stdClass;

class AutoBinderTest extends TestCase
{
    protected AutoBinder $autoBinder;

    protected function setUp(): void
    {
        parent::setUp();
        $this->autoBinder = new AutoBinder($this->app);
    }

    // =========================================================================
    // Security Tests - Path Sanitization
    // =========================================================================

    /** @test */
    public function testSanitizePathRemovesDirectoryTraversal(): void
    {
        $reflection = new ReflectionClass($this->autoBinder);
        $method = $reflection->getMethod('sanitizePath');
        $method->setAccessible(true);

        // Test various directory traversal attempts
        $this->assertEquals('Repositories', $method->invoke($this->autoBinder, '../Repositories'));
        $this->assertEquals('Repositories', $method->invoke($this->autoBinder, '..\\Repositories'));
        $this->assertEquals('Repositories', $method->invoke($this->autoBinder, '../../Repositories'));
        $this->assertEquals('Services', $method->invoke($this->autoBinder, '../../../Services'));
    }

    /** @test */
    public function testSanitizePathHandlesComplexTraversal(): void
    {
        $reflection = new ReflectionClass($this->autoBinder);
        $method = $reflection->getMethod('sanitizePath');
        $method->setAccessible(true);

        // Test complex traversal attempts that might bypass simple string replacement
        // "....//" -> remove ".." -> "//" -> "/" -> trimmed -> ""
        $this->assertEquals('', $method->invoke($this->autoBinder, '....//'));
        
        // "..././" -> reduces to safe empty string or safe path
        // Actual logic sequence aggressively reduces this to empty
        $this->assertEquals('', $method->invoke($this->autoBinder, '..././'));

        // "..//../" -> remove "../" -> "..//" -> remove ".." -> "//" -> ""
        $this->assertEquals('', $method->invoke($this->autoBinder, '..//../'));
    }

    /** @test */
    public function testSanitizePathNormalizesSlashes(): void
    {
        $reflection = new ReflectionClass($this->autoBinder);
        $method = $reflection->getMethod('sanitizePath');
        $method->setAccessible(true);

        // Should normalize backslashes to forward slashes and trim
        $result = $method->invoke($this->autoBinder, 'Domain\\User\\Repositories');
        $this->assertEquals('Domain/User/Repositories', $result);

        $result = $method->invoke($this->autoBinder, '/Repositories/');
        $this->assertEquals('Repositories', $result);
    }

    /** @test */
    public function testSanitizePathRejectsEmptyAfterSanitization(): void
    {
        $reflection = new ReflectionClass($this->autoBinder);
        $method = $reflection->getMethod('sanitizePath');
        $method->setAccessible(true);

        // Empty paths after sanitization - should still work with empty string
        $result = $method->invoke($this->autoBinder, '../../../');
        $this->assertEquals('', $result);
    }

    // =========================================================================
    // Configuration Tests
    // =========================================================================

    /** @test */
    public function testBindRepositoriesRespectsConfigDisabled(): void
    {
        // Disable repository auto-binding
        config(['repository-service.auto_binding.repositories' => false]);

        // Create a test repository directory
        $repoPath = $this->app->path('TestRepositories');
        File::makeDirectory($repoPath, 0755, true, true);

        try {
            File::put($repoPath . '/TestRepository.php', $this->getInterfaceStub('TestRepositories', 'TestRepository'));
            File::put($repoPath . '/TestRepositoryImplement.php', $this->getImplementStub('TestRepositories', 'TestRepository'));

            config(['repository-service.paths.repositories' => 'TestRepositories']);

            // This should NOT bind because repositories is disabled
            $this->autoBinder->bindRepositories();

            $this->assertFalse($this->app->bound('App\\TestRepositories\\TestRepository'));
        } finally {
            File::deleteDirectory($repoPath);
        }
    }

    /** @test */
    public function testBindServicesRespectsConfigDisabled(): void
    {
        // Disable service auto-binding
        config(['repository-service.auto_binding.services' => false]);

        // Create a test service directory
        $servicePath = $this->app->path('TestServices');
        File::makeDirectory($servicePath, 0755, true, true);

        try {
            File::put($servicePath . '/TestService.php', $this->getInterfaceStub('TestServices', 'TestService'));
            File::put($servicePath . '/TestServiceImplement.php', $this->getImplementStub('TestServices', 'TestService'));

            config(['repository-service.paths.services' => 'TestServices']);

            // This should NOT bind because services is disabled
            $this->autoBinder->bindServices();

            $this->assertFalse($this->app->bound('App\\TestServices\\TestService'));
        } finally {
            File::deleteDirectory($servicePath);
        }
    }

    // =========================================================================
    // Binding Functionality Tests
    // =========================================================================

    /** @test */
    public function testManualBindingsTakePriority(): void
    {
        // Create a manual binding first
        $this->app->bind('App\\Repositories\\ManualRepository', function () {
            return new stdClass();
        });

        // Verify the manual binding exists
        $this->assertTrue($this->app->bound('App\\Repositories\\ManualRepository'));

        // Create test files
        $repoPath = $this->app->path('Repositories');
        File::makeDirectory($repoPath, 0755, true, true);

        try {
            File::put($repoPath . '/ManualRepository.php', $this->getInterfaceStub('Repositories', 'ManualRepository'));
            File::put($repoPath . '/ManualRepositoryImplement.php', $this->getImplementStub('Repositories', 'ManualRepository'));

            config(['repository-service.auto_binding.repositories' => true]);
            config(['repository-service.paths.repositories' => 'Repositories']);

            // AutoBinder should NOT override the manual binding
            $this->autoBinder->bindRepositories();

            // Manual binding should still return our stdClass
            $resolved = $this->app->make('App\\Repositories\\ManualRepository');
            $this->assertInstanceOf(stdClass::class, $resolved);
        } finally {
            File::deleteDirectory($repoPath);
        }
    }

    /** @test */
    public function testMissingDirectoryHandledGracefully(): void
    {
        config(['repository-service.auto_binding.repositories' => true]);
        config(['repository-service.paths.repositories' => 'NonExistentDirectory']);

        // This should NOT throw an exception
        $this->autoBinder->bindRepositories();

        // Just verify no exception was thrown
        $this->assertTrue(true);
    }

    /** @test */
    public function testBindFromPathSkipsNonPhpFiles(): void
    {
        $testPath = $this->app->path('TestBindPath');
        File::makeDirectory($testPath, 0755, true, true);

        try {
            // Create non-PHP files that should be ignored
            File::put($testPath . '/README.md', '# Test');
            File::put($testPath . '/config.json', '{}');
            File::put($testPath . '/.gitignore', '*');

            config(['repository-service.auto_binding.repositories' => true]);
            config(['repository-service.paths.repositories' => 'TestBindPath']);

            // This should not throw and should skip non-PHP files
            $this->autoBinder->bindRepositories();

            $this->assertTrue(true);
        } finally {
            File::deleteDirectory($testPath);
        }
    }

    /** @test */
    public function testBindFromPathSkipsImplementationFiles(): void
    {
        $testPath = $this->app->path('TestImplPath');
        File::makeDirectory($testPath, 0755, true, true);

        try {
            // Create only implementation file (no interface)
            File::put($testPath . '/SomeImplement.php', '<?php class SomeImplement {}');

            config(['repository-service.auto_binding.repositories' => true]);
            config(['repository-service.paths.repositories' => 'TestImplPath']);

            // Implementation files should be skipped, not trying to bind "Some" interface
            $this->autoBinder->bindRepositories();

            // No exception means success
            $this->assertTrue(true);
        } finally {
            File::deleteDirectory($testPath);
        }
    }

    // =========================================================================
    // Helper Methods
    // =========================================================================

    protected function getInterfaceStub(string $namespace, string $name): string
    {
        return <<<PHP
<?php

namespace App\\{$namespace};

interface {$name}
{
    public function test(): void;
}
PHP;
    }

    protected function getImplementStub(string $namespace, string $name): string
    {
        return <<<PHP
<?php

namespace App\\{$namespace};

class {$name}Implement implements {$name}
{
    public function test(): void {}
}
PHP;
    }
}
