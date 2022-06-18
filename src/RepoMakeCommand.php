<?php

namespace Ssmalik99\Repostarry;

use Illuminate\Console\Concerns\CreatesMatchingTest;
use Illuminate\Console\GeneratorCommand;
use InvalidArgumentException;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputOption;

#[AsCommand(name: 'make:repo')]
class RepoMakeCommand extends GeneratorCommand
{
    use CreatesMatchingTest;

    /*
    * Command name
    *
    * @var string
    */
    protected $name = 'make:repo';

    
    /**
     * The name of the console command.
     *
     * This name is used to identify the command during lazy loading.
     *
     * @var string|null
     *
     * @deprecated
     */
    protected static $defaultName = 'make:repo';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new repository with interface and self service provider class';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Repository';

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    // protected function getStub()
    // {
    //     $stub = null;

    //     if ($type = $this->option('type')) {
    //         $stub = "/stubs/controller.{$type}.stub";
    //     } elseif ($this->option('parent')) {
    //         $stub = '/stubs/controller.nested.stub';
    //     } elseif ($this->option('model')) {
    //         $stub = '/stubs/controller.model.stub';
    //     } elseif ($this->option('invokable')) {
    //         $stub = '/stubs/controller.invokable.stub';
    //     } elseif ($this->option('resource')) {
    //         $stub = '/stubs/controller.stub';
    //     }

    //     if ($this->option('api') && is_null($stub)) {
    //         $stub = '/stubs/controller.api.stub';
    //     } elseif ($this->option('api') && ! is_null($stub) && ! $this->option('invokable')) {
    //         $stub = str_replace('.stub', '.api.stub', $stub);
    //     }

    //     $stub ??= '/stubs/controller.plain.stub';

    //     return $this->resolveStubPath($stub);
    // }


}