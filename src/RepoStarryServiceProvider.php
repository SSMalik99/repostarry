<?php

namespace Ssmalik99\Repostarry;

use Illuminate\Support\ServiceProvider;

class RepoStarryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
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
            ]);
        }


        $this->publishes([
            __DIR__.'/../config/starry.php' => config_path('starry.php')
        ], 'starry-config');

        $this->mergeConfigFrom(
            __DIR__.'/../config/starry.php', 'starry'
        );
    }

}
