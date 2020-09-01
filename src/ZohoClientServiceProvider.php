<?php

namespace Avant\LaravelZohoClient;

use Avant\LaravelZohoClient\OAuth2\Provider;
use Illuminate\Support\ServiceProvider;

class ZohoClientServiceProvider extends ServiceProvider
{
    public const TAG = 'zoho-client';

    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/config.php', self::TAG);

        $this->app->singleton(Provider::class, function () {
            return new Provider(config(self::TAG . '.client_id'), config(self::TAG . '.client_secret'));
        });
    }

    public function boot()
    {
        $this->loadRoutesFrom(__DIR__ . '/../routes/web.php');

        if ($this->app->runningInConsole()) {
            $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
            $this->publishes([__DIR__ . '/../config/config.php' => config_path(self::TAG . '.php')], 'config');
        }
    }
}
