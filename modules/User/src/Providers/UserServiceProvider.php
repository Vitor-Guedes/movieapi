<?php

namespace Modules\User\Providers;

use Illuminate\Auth\Middleware\Authenticate;
use Illuminate\Support\ServiceProvider;

class UserServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->loadMigrationsFrom(__DIR__ . '/../../database/migrations');

        $this->loadRoutesFrom(__DIR__ . '/../Routes/api.php');

        if (\Composer\InstalledVersions::isInstalled('modules/user')) {
            Authenticate::redirectUsing(fn  () => route('v1.api.user.unauthorized'));
        } 
    }

    public function register()
    {
        $this->replaceConfigRecursivelyFrom(__DIR__ . '/../Config/auth.php', 'auth');
    }
}