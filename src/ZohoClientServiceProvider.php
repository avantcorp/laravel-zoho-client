<?php

namespace Avant\ZohoClient;

use Avant\ZohoClient\OAuth2\Provider;
use Illuminate\Support\ServiceProvider;

class ZohoClientServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/zoho_client.php', 'zoho_client');

        $this->app->singleton(Provider::class, fn () => new Provider(
            clientId     : config('services.zoho_client.client_id'),
            clientSecret : config('services.zoho_client.client_secret'),
            redirectUri  : route('zoho-client.callback'),
            defaultScopes: config('services.zoho_client.scopes')
        ));
    }

    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__.'/../routes/web.php');

        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/zoho_client.php' => config_path('zoho_client.php'),
            ], 'config');
        }
    }
}
