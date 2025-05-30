<?php

namespace Avant\Zoho\Commands;

use Avant\Zoho\OAuth2\Provider;
use Illuminate\Console\Command;
use Illuminate\Support\Uri;
use Spatie\Valuestore\Valuestore;

class AuthenticateCommand extends Command
{
    protected $signature = 'zoho:authenticate';
    protected $description = 'OAuth authentication with Zoho';

    public function handle(Provider $provider): int
    {
        $this->output->section('Authorization URL');
        $this->output->writeln($provider->getAuthorizationUrl([
            'access_type' => 'offline',
            'prompt'      => 'consent',
        ]));
        $state = $provider->getState();

        $callbackUrl = Uri::of($this->output->ask('Callback URL'));
        validator([
            'state'              => $callbackUrl->query()->get('state'),
            'state_confirmation' => $state,
        ], [
            'state' => ['required', 'string', 'confirmed'],
        ])->validate();

        $token = $provider->getAccessToken(
            'authorization_code',
            ['code' => $callbackUrl->query()->get('code')]
        );

        Valuestore::make(config('services.zoho.token_storage_path'), [
            'token'         => $token,
            'refresh_token' => $token->getRefreshToken(),
        ]);

        return static::SUCCESS;
    }
}