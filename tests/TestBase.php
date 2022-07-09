<?php
namespace Ssmalik99\Repostarry\Tests;

use Orchestra\Testbench\TestCase;
use Ssmalik99\Repostarry\StarryInitCommand;
use Ssmalik99\Repostarry\StarryMakeCommand;

// \PHPUnit_Framework_TestCase
class TestBase extends TestCase
{
     public function setUp(): void
    {
        \Illuminate\Console\Application::starting(function ($artisan) {
            $artisan->resolveCommands([StarryMakeCommand::class, StarryInitCommand::class]);
            // $artisan->call("starry:launch");
        });
        parent::setUp();
    }


 
}