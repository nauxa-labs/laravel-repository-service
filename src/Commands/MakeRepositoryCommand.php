<?php

declare(strict_types=1);

namespace Refinaldy\RepositoryService\Commands;

use Illuminate\Console\GeneratorCommand;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputOption;

#[AsCommand(name: 'make:repository')]
class MakeRepositoryCommand extends GeneratorCommand
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'make:repository';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new repository interface and implementation class';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Repository';

    /**
     * Execute the console command.
     *
     * @return bool|null
     */
    public function handle()
    {
        $name = $this->getNameInput();

        // Generate Interface
        if ($this->generateInterface($name)) {
            $this->components->info("Repository interface [{$name}] created successfully.");
        }

        // Generate Implementation
        if ($this->generateImplementation($name)) {
            $this->components->info("Repository implementation [{$name}Implement] created successfully.");
        }

        return self::SUCCESS;
    }

    /**
     * Generate the interface file.
     */
    protected function generateInterface(string $name): bool
    {
        $interfaceName = $this->qualifyClass($name);
        $path = $this->getPath($interfaceName);

        if (!$this->option('force') && $this->alreadyExists($name)) {
            $this->components->error("Repository interface [{$name}] already exists.");
            return false;
        }

        $this->makeDirectory($path);

        $stub = $this->getInterfaceStub();
        $content = $this->replaceNamespace($stub, $interfaceName)->replaceClass($stub, $interfaceName);

        $this->files->put($path, $this->sortImports($content));

        return true;
    }

    /**
     * Generate the implementation file.
     */
    protected function generateImplementation(string $name): bool
    {
        $implementName = $this->qualifyClass($name . 'Implement');
        $path = $this->getPath($implementName);

        if (!$this->option('force') && $this->files->exists($path)) {
            $this->components->error("Repository implementation [{$name}Implement] already exists.");
            return false;
        }

        $this->makeDirectory($path);

        $stub = $this->getImplementStub();
        $content = $this->buildImplementClass($stub, $implementName, $name);

        $this->files->put($path, $this->sortImports($content));

        return true;
    }

    /**
     * Build the implementation class content.
     */
    protected function buildImplementClass(string $stub, string $name, string $interfaceName): string
    {
        $stub = $this->replaceNamespace($stub, $name)->replaceClass($stub, $name);

        // Replace model placeholder
        $model = $this->option('model') ?? $this->guessModelName($interfaceName);
        $stub = str_replace(['{{ model }}', '{{model}}'], $model, $stub);

        // Replace interface name placeholder
        $stub = str_replace(['{{ interfaceName }}', '{{interfaceName}}'], $interfaceName, $stub);

        return $stub;
    }

    /**
     * Guess the model name from the repository name.
     */
    protected function guessModelName(string $name): string
    {
        // Remove "Repository" suffix to get model name
        return preg_replace('/Repository$/i', '', class_basename($name)) ?: 'Model';
    }

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return $this->getInterfaceStub();
    }

    /**
     * Get the interface stub file.
     */
    protected function getInterfaceStub(): string
    {
        $customPath = $this->laravel->basePath('stubs/repository.interface.stub');

        return file_exists($customPath)
            ? $this->files->get($customPath)
            : $this->files->get($this->getDefaultInterfaceStub());
    }

    /**
     * Get the implementation stub file.
     */
    protected function getImplementStub(): string
    {
        $customPath = $this->laravel->basePath('stubs/repository.implement.stub');

        return file_exists($customPath)
            ? $this->files->get($customPath)
            : $this->files->get($this->getDefaultImplementStub());
    }

    /**
     * Get the default interface stub path.
     */
    protected function getDefaultInterfaceStub(): string
    {
        return __DIR__ . '/../../stubs/repository.interface.stub';
    }

    /**
     * Get the default implementation stub path.
     */
    protected function getDefaultImplementStub(): string
    {
        return __DIR__ . '/../../stubs/repository.implement.stub';
    }

    /**
     * Get the default namespace for the class.
     *
     * @param string $rootNamespace
     * @return string
     */
    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace . '\Repositories';
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [
            ['model', 'm', InputOption::VALUE_OPTIONAL, 'The model that the repository applies to'],
            ['force', 'f', InputOption::VALUE_NONE, 'Create the class even if the repository already exists'],
        ];
    }
}
