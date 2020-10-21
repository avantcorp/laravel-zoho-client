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
            $defaultScopes = config('services.zoho_client.scopes');

            return new Provider(
                config('services.zoho_client.client_id'),
                config('services.zoho_client.client_secret'),
                route('zohoClient.callback'),
                is_array($defaultScopes) ? $defaultScopes : array_map('trim', explode(',', $defaultScopes))
            );
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
