<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Webpatser\Uuid\Uuid;

class MakeUuidCommand extends Command {

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'make:uuid';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "Generates a Universal Unique ID";

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $this->info(Uuid::generate()->string);
    }

}
