<?php

namespace Ssmalik99\Repostarry\Traits;

/**
 * Binding trait to add things in the file
 */
trait BindingTrait
{
    
    public function mergeConfigBinding(array $bindings = [])
    {
        $filePath = config_path('starry.php');
        $content = config('starry.bindings');

        if($this->files->missing($filePath)):
            $content = [];
        endif;

        $content = $content ?? [];
        $content = array_merge($content, $bindings);

        $stub = $this->resolveStubPath("/stubs/starry.config.stub");
        $stub = $this->files->get($stub);

        $bindingString = "'bindings' => [";
        
        foreach ($content as $interface => $repository) {
            $bindingString .= "\n\t\t\\" . $interface . "::class => \\" . $repository . "::class,\n\n";
        }

        $bindingString .= "\t],";

        $stub = str_replace('{{ bindings }}', $bindingString, $stub);
        // dd($stub, $bindingString, file_exists($filePath));
        // file_exists($filePath);
        $this->files->put($filePath, $stub);
        
    }


    public function basicSetupImplemented()
    {
        $basicSetupClasses = [
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

    protected function deleteBasicSetupFiles()
    {

        $starries = config('starry.bindings');
        
        foreach ($starries as $interface => $repo) {
            
            $this->files->delete($this->getPath($interface));
            $this->files->delete($this->getPath($repo));
            
        }

        
        $stub = $this->resolveStubPath("/stubs/starry.config.stub");
        $stub = $this->files->get($stub);
        
        $bindingString = "'bindings' => []";
        $stub = str_replace('{{ bindings }}', $bindingString, $stub);
        
        $this->files->replace(config_path('starry.php'), $stub);
    }
}
