<?php

namespace Ssmalik99\Repostarry\Traits;

/**
 * Binding trait to add things in the file
 */
trait BindingTrait
{
    public function mergeBinding(array $bindings)
    {
        $filePath = config_path('starry.php');
        $content = config('starry.bindings');

        if($this->files->missing($filePath)):
            $content = [];
        endif;

        $content = array_merge($content, $bindings);

        $stub = $this->resolveStubPath("/stubs/starry.config.stub");
        
        $bindingString = "";
        
        foreach ($content as $interface => $repository) {
            $bindingString .= "\t\\" . $interface . "::class => \\" . $repository . "::class,\n";
        }

        $stub = str_replace('{{ bindings }}', $bindingString, $stub);
        $this->files->putContent($filePath, $stub);
        
    }
}
