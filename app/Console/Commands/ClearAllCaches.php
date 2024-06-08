<?php
// app/Console/Commands/ClearAllCaches.php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ClearAllCaches extends Command
{
    protected $signature = 'cache:clear-all';
    protected $description = 'Clear all application caches';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $this->call('cache:clear');
        $this->call('route:clear');
        $this->call('config:clear');
        $this->call('view:clear');
        $this->call('clear-compiled');
        $this->call('optimize:clear');

        $this->info('All caches have been cleared successfully.');
    }
}

