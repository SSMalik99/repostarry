<?php

namespace Ssmalik99\Repostarry;

use Illuminate\Console\Concerns\CreatesMatchingTest;
use Illuminate\Console\GeneratorCommand;
use InvalidArgumentException;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputOption;
use Illuminate\Support\Facades\Storage;
use Ssmalik99\Repostarry\Traits\BindingTrait;
// use Illuminate\Console\Command;

#[AsCommand(name: 'starry:launch')]
class StarryInitCommand extends GeneratorCommand
{

    use BindingTrait;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $name = 'starry:launch';

    protected static $defaultName = "starry:launch";


    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Basic Setup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This will setup a basic repository system for our application';
    
    protected function getStub()
    {
        $stub = "/stubs/starry.repository.base.stub";
        return $this->resolveStubPath($stub); 
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

    protected function getArguments()
    {
        return [];
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
     * Build the class with the given name.
     *
     * Remove the base controller import if we are already in the base namespace.
     *
     * @param  string  $name
     * @return string
     */
    protected function buildMyMethodClass($name, $stub, $interfaceName = null)
    {
        $stub = $this->files->get($stub);
        return $this->replaceNamespace($stub, $name)->replaceInterface($stub, $interfaceName)->replaceClass($stub, $name);
    }

    protected function baseInterfaceName($interface)
    {
        return $this->rootNamespace()."Repository\\".config('starry.starry_interfaces_path')."\\".$interface;
    }

    protected function replaceInterface(&$stub, $interfaceName)
    {
        // []
        $searches = [
            ['{{ interfaceName }}', '{{ interfaceNameSapce }}']
        ];

        foreach ($searches as $search) {
            $stub = str_replace(
                $search,
                [$interfaceName, $this->baseInterfaceName($interfaceName)],
                $stub
            );
        }

        return $this;
    }

    

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {

        // Publish the vendor

        $basicSetupClasses = [
            // [
            //     "name" => "App\\Providers\\RepositoryServiceProvider",
            //     "type" => "provider",
            //     "stub" => $this->resolveStubPath("/stubs/starry.provider.stub")
            // ],
            [
                "name" => "App\\Repository\\".config('starry.starry_interfaces_path')."\\".config('starry.starry_data_model')."RepositoryInterface",
                
                "interface" => config('starry.starry_data_model')."RepositoryInterface",
                
                "type" => "interface",
                
                "stub" => $this->resolveStubPath("/stubs/starry.interface.base.stub"),

            ],
            [
                "name" => "App\\Repository\\".config('starry.starry_repository_path')."\\"."BaseRepository",
                
                "interface" => config('starry.starry_data_model')."RepositoryInterface",
                
                "type" => "class",
                
                "stub" => $this->resolveStubPath("/stubs/starry.repository.base.stub")
            ],
            
        ];

        $basicBindings = [
            "App\\Repository\\".config('starry.starry_interfaces_path')."\\".config('starry.starry_data_model')."RepositoryInterface" => "App\\Repository\\".config('starry.starry_repository_path')."\\"."BaseRepository"
        ];

        foreach ($basicSetupClasses as $setup) {
            
            $path = $this->getPath($setup["name"]);
            $this->makeDirectory($path);
            $this->files->put(
                $path, 
                $this->sortImports(
                        $this->buildMyMethodClass(
                        $setup["name"], 
                        $setup["stub"],
                        $setup["interface"] ?? null
                    )
                )
            );
            
        }
        
        $this->info($this->type.' created successfully.');
        $this->line("Thanks for installing out package, you can also give us a star on github.");
        $this->info("Follow Link: https://github.com/SSMalik99/repostarry");

        $this->call("vendor:publish", [
            "--tag" => "starry-config"
        ]);

        $this->mergeBinding($basicBindings);

        // if (in_array(CreatesMatchingTest::class, class_uses_recursive($this))) {
        //     $this->handleTestCreation($path);
        // }
    }


}
