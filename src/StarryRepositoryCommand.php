<?php

namespace Ssmalik99\Repostarry;

use Illuminate\Support\Str;
use Illuminate\Console\Concerns\CreatesMatchingTest;
use Illuminate\Console\GeneratorCommand;
use InvalidArgumentException;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputOption;
// use Illuminate\Console\Command;

#[AsCommand(name: 'starry:repo')]
class StarryRepositoryCommand extends GeneratorCommand
{

    
    // use CreatesMatchingTest;

    /*
    * Command name
    *
    * @var string
    */
    protected $name = 'starry:repo';

    protected $interfaceNameSpace, $interfaceName;
    /**
     * The name of the console command.
     *
     * This name is used to identify the command during lazy loading.
     *
     * @var string|null
     *
     * @deprecated
     */
    // protected $signature = 'starry:repo';
    protected static $defaultName = 'starry:repo';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new repository with interface';

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


    public function basicSetupImplemented()
    {
        $basicSetupClasses = [
            // [
            //     "name" => "App\\Providers\\RepositoryServiceProvider",
            //     "type" => "provider",
            // ],
            [
                "name" => "App\\Repository\\".config('starry.starry_interfaces_path')."\\".config('starry.starry_data_model')."RepositoryInterface",
                "type" => "interface",
            ],
            [
                "name" => "App\\Repository\\".config('starry.starry_repository_path')."\\"."BaseRepository",
                "type" => "class",
            ],
        ];
        
        try {
            foreach ($basicSetupClasses as $setup) {
                switch ($setup["type"]) {
                    case 'interface':
                        if (!interface_exists($setup["name"])) {
                            return false;
                        }
                        break;
                    
                    case "class":
                        if (!class_exists($setup["name"])) {
                            
                            return false;
                        }                   
                        break;
                }
            }
        } catch (\Throwable $th) {
            return false;
        }
        return true;
    }



    protected function getStub()
    {
        $stu = null;
        
        if ($this->option('model')) {
            $stub = '/stubs/starry.repository.model.stub';
        }

        $stub ??= '/stubs/starry.repository.stub';

        return $this->resolveStubPath($stub);
    }

    
    /**
     * Get the default namespace for the class.
     *
     * @param  string  $rootNamespace
     * @return string
     */
    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace.'\Repository\\'.config('starry.starry_repository_path');
    }

    /**
     * Build the class with the given name.
     *
     * Remove the base controller import if we are already in the base namespace.
     *
     * @param  string  $name
     * @return string
     */
    protected function buildClass($name)
    {
        $repositoryNamespace = $this->getNamespace($name);

        $replace = [];

        if ($this->option('model')) {
            $replace = $this->buildModelReplacements();
        }

        $replace = array_merge(
                $replace,
                $this->buildBaseRepoReplacement(), 
                $this->buildInterfaceReplacement()
            );

        $replace["use {$repositoryNamespace}\Repository;\n"] = '';

        return str_replace(
            array_keys($replace), array_values($replace), parent::buildClass($name)
        );
    }

    protected function buildInterfaceReplacement()
    {
        return [
            "{{ interfaceNameSapce }}" => $this->interfaceNameSpace,
            "{{ interfaceName }}" => $this->interfaceName
        ];
    }

    protected function buildBaseRepoReplacement()
    {
        
        return [
            "{{ BaseRepoPath }}" => $this->rootNameSpace().'Repository\\'.config('starry.starry_repository_path')
        ];
    }
    
    /**
     * Get the fully-qualified model class name.
     *
     * @param  string  $model
     * @return string
     *
     * @throws \InvalidArgumentException
     */
    protected function parseModel($model)
    {
        if (preg_match('([^A-Za-z0-9_/\\\\])', $model)) {
            throw new InvalidArgumentException('Model name contains invalid characters.');
        }

        return $this->qualifyModel($model);
    }


    /**
     * Build the model replacement values.
     *
     * @param  array  $replace
     * @return array
     */
    protected function buildModelReplacements()
    {
        $modelClass = $this->parseModel($this->option('model'));

        if (! class_exists($modelClass) && $this->confirm("A {$modelClass} model does not exist. Do you want to generate it?", true)) {
            $this->call('make:model', ['name' => $modelClass]);
        }

        return [
            'DummyFullModelClass' => $modelClass,
            '{{ namespacedModel }}' => $modelClass,
            '{{namespacedModel}}' => $modelClass,
            'DummyModelClass' => class_basename($modelClass),
            '{{ model }}' => class_basename($modelClass),
            '{{model}}' => class_basename($modelClass),
            'DummyModelVariable' => lcfirst(class_basename($modelClass)),
            '{{ modelVariable }}' => lcfirst(class_basename($modelClass)),
            '{{modelVariable}}' => lcfirst(class_basename($modelClass)),
        ];
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [
            ['force', null, InputOption::VALUE_NONE, 'Create the class even if the repository already exists'],
            ['model', 'm', InputOption::VALUE_OPTIONAL, 'Generate a resource repository for the given model.']
        ];
    }

    /**
     * Resolve the fully-qualified path to the stub.
     *
     * @param  string  $stub
     * @return string
     */
    protected function resolveStubPath($stub)
    {
        return file_exists($customPath = $this->laravel->basePath(trim($stub, '/')))
                        ? $customPath
                        : __DIR__.$stub;
    }

    protected function qualifyInterfaceClass($name)
    {
        $name = ltrim($name, '\\/');

        $name = str_replace('/', '\\', $name);

        $rootNamespace = $this->rootNamespace();

        if (Str::startsWith($name, $rootNamespace)) {
            return $name;
        }

        return trim($rootNamespace, '\\').'\\Repository\\'.config('starry.starry_interfaces_path')."\\".$name;
    }


    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        if(!$this->basicSetupImplemented()):

            if ($this->confirm("Basic setup is not done for Starry. Do you want to setup it?", true)) {
                $this->call('starry:launch');
            }else{
                return;
            }
            
        endif;

        // First we need to ensure that the given name is not a reserved word within the PHP
        // language and that the class name will actually be valid. If it is not valid we
        // can error now and prevent from polluting the filesystem using invalid files.
        if ($this->isReservedName($this->getNameInput())) {
            $this->error('The name "'.$this->getNameInput().'" is reserved by PHP.');

            return false;
        }

        $this->interfaceName = str_contains($this->getNameInput(), "Interface") ? $this->getNameInput() : $this->getNameInput()."Interface";

        $this->interfaceNameSpace = $this->qualifyInterfaceClass($this->interfaceName);
        
        $interfaceCreated = $this->call("starry:interface", [
            "name" => $this->interfaceName,
            !$this->option("model") ?: "-m" => $this->option("model"),
            !$this->option('force') ?: "--force" => true,
        ]);
        
        if($interfaceCreated):

            $name = $this->qualifyClass($this->getNameInput());
            $path = $this->getPath($name);

            if ((! $this->hasOption('force') ||
                ! $this->option('force')) &&
                $this->alreadyExists($this->getNameInput())) {
                $this->error($this->type.' already exists!');

                return false;
            }

            $this->makeDirectory($path);
            $this->files->put($path, $this->sortImports($this->buildClass($name)));
            $this->info($this->type.' created successfully.');

            
            // if (in_array(CreatesMatchingTest::class, class_uses_recursive($this))) {
            //     $this->handleTestCreation($path);
            // }
        else:
            $this->warn("You probably entered wrong input");
            return false;
        endif;

        return true;
    }


}