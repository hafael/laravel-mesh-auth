<?php

namespace Hafael\Mesh\Auth;

use Illuminate\Support\ServiceProvider;

class MeshAuthServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->publishes([
            __DIR__.'/../config/auth-mesh.php' => config_path('auth-mesh.php')
        ], 'auth-mesh-config');

        $this->publishes([
            __DIR__.'/../database/migrations/' => database_path('migrations')
        ], 'auth-mesh-migrations');

        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');

        $this->loadRoutesFrom(__DIR__.'/../routes/api.php');

    }

    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/auth-mesh.php', 'auth-mesh'
        );
    }
}