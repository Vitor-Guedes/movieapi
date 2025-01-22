<?php

namespace Modules\Movie\Providers;

use Illuminate\Support\ServiceProvider;
use Modules\Movie\Console\Commands\ImportFromDataset;

class MovieServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->loadMigrationsFrom(__DIR__ . '/../../database/migrations');
    }

    public function register()
    {
        $this->commands([
            ImportFromDataset::class
        ]);
    }
}