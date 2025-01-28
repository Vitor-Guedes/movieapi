<?php

namespace Modules\Movie\Providers;

use Illuminate\Support\ServiceProvider;
use Modules\Movie\Console\Commands\GenerateSwagger;
use Modules\Movie\Console\Commands\ImportFromDataset;

class MovieServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->loadMigrationsFrom(__DIR__ . '/../../database/migrations');
        
        $this->loadRoutesFrom(__DIR__ . '/../Routes/api.php');
    }

    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../Config/api.php', 'movie');

        $this->commands([
            GenerateSwagger::class,
            ImportFromDataset::class
        ]);
    }
}