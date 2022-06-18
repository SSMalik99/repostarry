<?php

namespace Ssmalik99\Repostarry;

use Illuminate\Console\Command;

class StarryInitCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'starry:launch';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This will setup a basic repository system for our application';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->line("I'm ready to fly");
    }
}
