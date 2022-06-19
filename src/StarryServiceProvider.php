<?php

namespace Ssmalik99\Repostarry;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Config;

class StarryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        
        $bindings = config('starry.bindings', []);

        foreach ($bindings as $interface => $repository) {
            
            $this->app->bind(
                $interface,
                $repository
            );
        }
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {

        if ($this->app->runningInConsole()) {
            $this->commands([
                StarryInitCommand::class,
                StarryMakeCommand::class,
                StarryInterfaceCommand::class,
                StarryRepositoryCommand::class
            ]);
        }


        $this->publishes([
            __DIR__.'/../config/starry.php' => config_path('starry.php')
        ], 'starry-config');

        $this->mergeConfigFrom(
            __DIR__.'/../config/starry.php', 
            'starry'
        );
    }

}
