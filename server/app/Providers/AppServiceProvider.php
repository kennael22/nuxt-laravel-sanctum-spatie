<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //check that app is local
        // if ($this->app->isLocal()) {
        //     //if local register your services you require for development
        //     $this->app->register('Barryvdh\Debugbar\ServiceProvider');
        // } else {
        // //else register your services you require for production
        //     $this->app['request']->server->set('HTTPS', true);
        // }
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Route::macro('customRoleResource', function ($uri, $controller) {
            Route::post("{$uri}/remove/{role?}", "{$controller}@destroyRole")->name("{$uri}.remove");
            Route::resource($uri, $controller)->except(['create', 'show', 'edit', 'destroy']);
        });
    }
}
