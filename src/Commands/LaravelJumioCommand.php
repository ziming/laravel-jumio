<?php

namespace Ziming\LaravelJumio\Commands;

use Illuminate\Console\Command;

class LaravelJumioCommand extends Command
{
    public $signature = 'laravel-jumio';

    public $description = 'My command';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}
