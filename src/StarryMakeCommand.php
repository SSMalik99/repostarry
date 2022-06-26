<?php

namespace Ssmalik99\Repostarry;

use Illuminate\Console\Concerns\CreatesMatchingTest;
use Illuminate\Console\GeneratorCommand;
use InvalidArgumentException;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputOption;
use Ssmalik99\Repostarry\Traits\BindingTrait;
use Symfony\Component\Console\Input\InputArgument;
use Illuminate\Support\Str;
use Exception;

#[AsCommand(name: 'starry:make')]
class StarryMakeCommand extends GeneratorCommand
{

    
    // use CreatesMatchingTest;
    use BindingTrait;

    /*
    * Command name
    *
    * @var string
    */
    protected $name = 'starry:make';

    /**
     * The name of the console command.
     *
     * This name is used to identify the command during lazy loading.
     *
     * @var string|null
     *
     * @deprecated
     */
    // protected $signature = 'starry:make';
    protected static $defaultName = 'starry:make';

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
    protected $type = 'Starry';

    protected $repositoryNameSpace, $repositoryName, $model, $force, $interfaceNameSpace, $interfaceName;

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        $repositoryStub = null;
        $interfaceStup = null;
        
        // As model is required we changed this to the direct stub
        $repositoryStub = '/stubs/starry.repository.model.stub';
        $interfaceStup = "/stubs/starry.interface.model.stub";

        
        return $this->resolveStubPath($repositoryStub);
        // return [
        //     "repositoryStub" => $this->resolveStubPath($repositoryStub),
        //     "interfaceStup" => $this->resolveStubPath($interfaceStup)
        // ];
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return [
            ['name', InputArgument::REQUIRED, 'The name of the class'],
            ['model', InputArgument::REQUIRED, 'Generate a resource repository for the given model.']
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
            ['force', null, InputOption::VALUE_NONE, 'Create the class even if the repository already exists']
        ];
    }

    /**
     * Check whether model is available or not
     * it will also execute the command to make a model
    */
    protected function checkModelAvailability()
    {
        $modelClass = $this->parseModel($this->model);
        
        
        if ( !class_exists($modelClass) ) {
            
            if($this->confirm("A {$modelClass} model does not exist. Do you want to generate it?", true)) {
                
                $this->call('make:model', ['name' => $modelClass]);
                return true;

            }else{

                return false;
            }
            
        }

        return true;

    }

    /**
     * Build model replacements for the stub files
     */
    protected function buildModelReplacements()
    {
        $modelClass = $this->parseModel($this->model);

        // if (! class_exists($modelClass) && $this->confirm("A {$modelClass} model does not exist. Do you want to generate it?", true)) {
        //     $this->call('make:model', ['name' => $modelClass]);
        // }

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
     * Build Base repository replacements for the stub files
     */
    protected function buildBaseRepoReplacement()
    {
        return [
            "{{ BaseRepoPath }}" => $this->rootNameSpace().'Repository\\'.config('starry.starry_repository_path')
        ];
    }

    /**
     * Build interface replacements for the stub files
     */
    protected function buildInterfaceReplacement()
    {
        return [
            "{{ interfaceNameSapce }}" => $this->interfaceNameSpace,
            "{{ interfaceName }}" => $this->interfaceName
        ];
    }

    protected function parseModel($model)
    {
        if (preg_match('([^A-Za-z0-9_/\\\\])', $model)) {
            throw new InvalidArgumentException('Model name contains invalid characters.');
        }

        return $this->qualifyModel($model);
    }


    /**
     * Main function to check the validation to make things batter
     * Starry function
    */
    public function starryPermission()
    {
        // Check Model Availability
        if( !$this->checkModelAvailability() )
        {
            throw new Exception("{$this->model} doesn't exists, please create one", 1);
            
        }

        // interface check
        if($this->alreadyExists($this->interfaceNameSpace) && !$this->force ){
            throw new Exception("{$this->interfaceNameSpace} already exists, use force flag to overwrite.", 1);
        }

        // repository check
        if($this->alreadyExists($this->repositoryNameSpace) && !$this->force ){
            throw new Exception("{$this->repositoryNameSpace} already exists, use force flag to overwrite.", 1);
        }

    }

    protected function alreadyExists( $starryNameSpace )
    {
        return $this->files->exists( $this->getPath( $starryNameSpace ) );
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
            }else {
                return false;
            }
            
        endif;


        if ($this->isReservedName($this->getNameInput())) {
            $this->error('The name "'.$this->getNameInput().'" is reserved by PHP.');
            return false;
        }

        $input = $this->getNameInput();
        
        $this->repositoryName = str_contains($input, "Repository") ? $input : $input."Repository";
        $this->repositoryNameSpace =  $this->qualifyRepositoryClass($this->repositoryName);

        $this->model = trim($this->argument('model'));
        $this->force = $this->option('force');

        $this->interfaceName = str_contains($this->repositoryName, "Interface") ? $this->repositoryName : $this->repositoryName."Interface";
        $this->interfaceNameSpace = $this->qualifyInterfaceClass($this->interfaceName);

        try {
            
            $this->starryPermission();
            $this->starryMakeRepository();
            
            
            $this->mergeConfigBinding(
                [
                    "{$this->interfaceNameSpace}" => "{$this->repositoryNameSpace}"
                ]
            );



        } catch (\Throwable $th) {

            $this->error($th->getMessage());
            return false;
        }

        $this->info('Starry created successfully.');
        return true;

    }

    /**
     * ******************************************
     * REPOSITORY CREATION METHODS
     * 
     * ******************************************
     * */

    protected function qualifyRepositoryClass($name)
    {
        $name = ltrim($name, '\\/');

        $name = str_replace('/', '\\', $name);

        $rootNamespace = $this->rootNamespace();

        if (Str::startsWith($name, $rootNamespace)) {
            return $name;
        }

        return trim($rootNamespace, '\\').'\\Repository\\'.config('starry.starry_data_model')."\\".$name;
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

    protected function resolveStubPath($stub)
    {
        return file_exists($customPath = $this->laravel->basePath(trim($stub, '/')))
                        ? $customPath
                        : __DIR__.$stub;
    }

    public function buildRepositoryClass()
    {
        $stub = $this->files->get($this->resolveStubPath('/stubs/starry.repository.model.stub'));

        $repositoryNamespace = $this->getNamespace($this->repositoryNameSpace);

        $replace = $this->buildModelReplacements();

        $replace = array_merge(
                $replace,
                $this->buildBaseRepoReplacement(), 
                $this->buildInterfaceReplacement()
            );

        $replace["use {$repositoryNamespace}\Repository;\n"] = '';
        
        $stubRelation = $this->replaceNamespace($stub, $this->repositoryNameSpace)->replaceClass($stub, $this->repositoryNameSpace);

        return str_replace(
            array_keys($replace), array_values($replace), $stubRelation
        );
    }

    public function buildInterfaceClass()
    {
        $stub = $this->files->get($this->resolveStubPath("/stubs/starry.interface.model.stub"));

        $interfaceNamespace = $this->getNamespace($this->interfaceNameSpace);

        $replace = $this->buildModelReplacements();

        $replace = array_merge($replace, $this->buildInterfaceReplacement());

        $replace["use {$interfaceNamespace}\Repository;\n"] = '';

        $stubRelation = $this->replaceNamespace($stub, $this->interfaceNameSpace)->replaceClass($stub, $this->interfaceNameSpace);

        return str_replace(
            array_keys($replace), array_values($replace), $stubRelation
        );
    }

    public function starryMakeRepository()
    {   

        $this->starryMakeInterface();
        
        $path = $this->getPath($this->repositoryNameSpace);

        $this->makeDirectory($path);

        $this->files->put($path, $this->sortImports($this->buildRepositoryClass()));

        $this->info('Repository created successfully.');

        // $bindings = [
        //     "{$this->interfaceNameSpace}" => "{$this->repositoryNameSpace}"
        // ];

        // $this->mergeConfigBinding($bindings);

        return true;
    }

    public function starryMakeInterface()
    {
        
        

        $path = $this->getPath($this->interfaceNameSpace);
        
        // if ( !$this->force && $this->alreadyExists($this->getNameInput())) {
        //     $this->error('Interface already exists!');
        //     return false;
        // }

        $this->makeDirectory($path);

        $this->files->put($path, $this->sortImports($this->buildInterfaceClass()));

        $this->info('Interface created successfully.');

        return true;

    }


}