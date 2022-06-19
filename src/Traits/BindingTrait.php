<?php

namespace Ssmalik99\Repostarry\Traits;

/**
 * Binding trait to add things in the file
 */
trait BindingTrait
{
    public function mergeBinding(array $bindings = [])
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
            $bindingString .= "\t\\" . $interface . "::class => \\" . $repository . "::class,\n";
        }

        $bindingString .= "],";

        $stub = str_replace('{{ bindings }}', $bindingString, $stub);
        // dd($stub, $bindingString, file_exists($filePath));
        // file_exists($filePath);
        $this->files->put($filePath, $stub);
        
    }
}
