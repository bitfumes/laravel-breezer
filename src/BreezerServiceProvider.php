<?php

namespace Bitfumes\Breezer;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class BreezerServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/breezer.php', 'breezer');
        $this->publishThings();
        $this->loadViewsFrom(__DIR__ . '/Views', 'breezer');
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
        $this->registerRoutes();
    }

    /**
     * Register the package routes.
     *
     * @return void
     */
    private function registerRoutes()
    {
        Route::group($this->routeConfiguration(), function () {
            $this->loadRoutesFrom(__DIR__ . '/Http/routes.php');
        });
    }

    /**
    * Get the Blogg route group configuration array.
    *
    * @return array
    */
    private function routeConfiguration()
    {
        return [
            'namespace'  => "Bitfumes\Breezer\Http\Controllers",
            'middleware' => 'api',
            'prefix'     => 'api',
        ];
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        // Register facade
        $this->app->singleton('breezer', function () {
            return new Breezer();
        });
    }

    public function publishThings()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/Breezer.php' => config_path('breezer.php'),
            ], 'config');

            $this->publishes([
                __DIR__ . '/database/migrations/' => database_path('migrations'),
            ], 'breezer:migrations');
        }
    }
}
