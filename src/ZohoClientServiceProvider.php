<?php

namespace Avant\ZohoClient;

use Avant\ZohoClient\OAuth2\Provider;
use Illuminate\Support\ServiceProvider;

class ZohoClientServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/zoho_client.php', 'zoho_client');

        $this->app->singleton(Provider::class, function () {
            return new Provider(config('services.zoho.client_id'), config('services.zoho.client_secret'));
        });
    }

    public function boot()
    {
        $this->loadRoutesFrom(__DIR__.'/../routes/web.php');

        if ($this->app->runningInConsole()) {
            $this->loadMigrationsFrom(__DIR__.'/../database/migrations');

            $this->publishes([
                __DIR__.'/../config/zoho_client.php' => config_path('zoho_client.php'),
            ], 'config');

            $this->publishes([
                __DIR__.'/../database/migrations' => database_path('migrations'),
            ], 'migrations');
        }
    }
}
