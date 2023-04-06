<?php


namespace Shuvo\BdrenOauth;

use Illuminate\Support\ServiceProvider;

class BdrenOauthServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->loadRoutesFrom(__DIR__ . '/route.php');
        $this->loadViewsFrom(__DIR__ . '/views', 'dev');
        $this->loadMigrationsFrom(__DIR__ . '/migrations');


        $this->publishes([
            __DIR__ . '/config/bdren_oauth.php' => config_path('bdren_oauth.php'),
        ], 'oauth-config');
    }

    public function register()
    {
        parent::register();

        /** @var Router $router */
        $router = $this->app['router'];

    }
}
