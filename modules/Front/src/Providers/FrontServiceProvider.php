<?php

namespace Modules\Front\Providers;

use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;

class FrontServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->loadRoutesFrom(__DIR__ . '/../Routes/web.php');
        $this->loadViewsFrom(__DIR__ . '/../Resources/views', 'front');
    
        // Configure o Vite para usar o diretório do pacote como base
        // Vite::useBuildDirectory('/../modules/Front/public/build');
    }
}