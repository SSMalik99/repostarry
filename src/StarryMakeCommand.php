<?php

namespace Ssmalik99\Repostarry;

use Illuminate\Console\Concerns\CreatesMatchingTest;
use Illuminate\Console\GeneratorCommand;
use InvalidArgumentException;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputOption;
// use Illuminate\Console\Command;

#[AsCommand(name: 'starry:make')]
class StarryMakeCommand extends GeneratorCommand
{

    
    // use CreatesMatchingTest;

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

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */


    public function basicSetupImplemented()
    {
        $basicSetupClasses = [
            [
                "name" => "App\\Providers\\RepositoryServiceProvider",
                "type" => "class",
            ],
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
        $repositoryStub = null;
        $interfaceStup = null;
        
        if ($this->option('model')) {
            $repositoryStub = '/stubs/starry.repository.model.stub';
            $interfaceStup = "/stubs/starry.interface.model.stub";
        }

        $repositoryStub ??= '/stubs/starry.repository.stub';
        $interfaceStup ??= "/stubs/starry.interface.stub";

        return $this->resolveStubPath($repositoryStub);
        // return [
        //     "repositoryStub" => $this->resolveStubPath($repositoryStub),
        //     "interfaceStup" => $this->resolveStubPath($interfaceStup)
        // ];
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

        // First we need to ensure that the given name is not a reserved word within the PHP
        // language and that the class name will actually be valid. If it is not valid we
        // can error now and prevent from polluting the filesystem using invalid files.
        if ($this->isReservedName($this->getNameInput())) {
            $this->error('The name "'.$this->getNameInput().'" is reserved by PHP.');

            return false;
        }

        $input = $this->getNameInput();

        $repositoryName = str_contains($input, "Repository") ? $input : $input."Repository";
        
        
        $repository = $this->call("starry:repo", [
            "name" => $repositoryName,
            !$this->option("model") ?: "-m" => $this->option("model"),
            !$this->option('force') ?: "--force" => true
        ]);

        if($repository):
            
            $this->info($this->type.' created successfully.');
            return true;

        endif;

        $this->error("please check above error.");
        return false;
        
        

        // if (in_array(CreatesMatchingTest::class, class_uses_recursive($this))) {
        //     $this->handleTestCreation($path);
        // }
    }


}