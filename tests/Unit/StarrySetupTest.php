<?php
namespace Ssmalik99\Repostarry\Tests;

use Ssmalik99\Repostarry\Tests\TestBase;


class StarrySetupTest extends TestBase
{

    /**
     * Test basic setup is implemented not for starry using command
    */
    public function test_starry_executing_basic_setup_command()
    {
        $this->artisan('starry:launch')
            ->expectsOutput("Basic Setup created successfully.")
            ->assertExitCode(1);
    }

    /**
     * Test starry is asking for confimation
    */
    public function test_starry_asking_to_check_basic_setup()
    {
        $this->artisan("make:starry User")
            ->expectsConfirmation("Basic setup is not done for Starry. Do you want to setup it?");
    }

    /**
     * Test starry is creating the repository system or not
    */
    public function test_starry_is_asking_for_the_recreation_of_unavailable_model()
    {
        $time = time();
        $name = "Starry/StarryTest".$time;
        $this->artisan("make:starry $name --test=starryTestingTestManuall")
            ->expectsConfirmation("A App\Models\Starry\StarryTest$time model does not exist. Do you want to generate it?", "no");
    }

    /**
     * Check starry is creating a model or not if model not available
    */
    public function test_starry_is_creating_repository()
    {
        $time = time();
        $name = "Starry/StarryTest".$time;

        $this->artisan("make:starry $name --test=starryTestingTestManuall")
            ->expectsConfirmation("A App\Models\Starry\StarryTest$time model does not exist. Do you want to generate it?", "yes")
            ->expectsOutput("Interface created successfully.")
            ->expectsOutput("Repository created successfully.")
            ->expectsOutput("Starry created successfully.");
    }

    /**
     * Check starry is creating unique repository
    */
    public function test_starry_creating_unique_repository($testCreatedStarry = false)
    {
        $time = time();
        $name = "Starry/FindUniqueStarryTest".$time;
        
        
        $this->artisan("make:starry $name --test=starryTestingTestManuall")
            ->expectsConfirmation("A App\Models\Starry\FindUniqueStarryTest$time model does not exist. Do you want to generate it?", "yes")
            ->expectsOutput("Interface created successfully.")
            ->expectsOutput("Repository created successfully.")
            ->expectsOutput("Starry created successfully.");
        
        // Check same input to check the manuall exit code
        $this->artisan("make:starry $name --test=starryTestingTestManuall")
            ->assertExitCode(0);
        
    }
}