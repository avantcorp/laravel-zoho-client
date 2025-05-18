<?php

namespace Avant\Zoho;

use Avant\Zoho\Commands\AuthenticateCommand;
use Avant\Zoho\OAuth2\Provider;
use Illuminate\Support\ServiceProvider;

class ZohoServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/services.zoho.php', 'services.zoho');

        $this->app->singleton(Provider::class, fn () => new Provider(
            clientId     : config('services.zoho.client_id'),
            clientSecret : config('services.zoho.client_secret'),
            redirectUri  : route('zoho.callback'),
            defaultScopes: config('services.zoho.scopes')
        ));
    }

    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__.'/../routes/web.php');

        $this->commands([
            AuthenticateCommand::class,
        ]);
    }
}
