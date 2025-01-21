<?php

namespace Modules\Movie\Providers;

use Illuminate\Support\ServiceProvider;
use Modules\Movie\Console\Commands\ImportFromDataset;

class MovieServiceProvider extends ServiceProvider
{
    public function boot()
    {
        // array_count
    }

    public function register()
    {
        $this->commands([
            ImportFromDataset::class
        ]);
    }
}