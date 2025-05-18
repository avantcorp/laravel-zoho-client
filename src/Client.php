<?php

namespace Avant\Zoho;

use Avant\Zoho\OAuth2\Provider;
use Carbon\Carbon;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;
use Spatie\Valuestore\Valuestore;

abstract class Client
{
    protected string $baseUrl;
    private ?Carbon $tokenExpiry = null;
    private ?string $token = null;

    protected function getBaseUrl(): string
    {
        return $this->baseUrl;
    }

    protected function request(): PendingRequest
    {
        return Http::baseUrl($this->getBaseUrl())
            ->asJson()
            ->acceptJson()
            ->withToken($this->getToken(), 'Zoho-oauthtoken');
    }

    private function getToken(): string
    {
        return cache()
            ->lock('zoho.token')
            ->block(10, function () {
                $store = Valuestore::make(config('services.zoho.token_storage_path'));
                if (is_null($this->tokenExpiry) || is_null($this->token)) {
                    $zohoAccessToken = $store->get('token');
                    $this->tokenExpiry = Carbon::createFromTimestamp($zohoAccessToken->token->getExpires());
                    $this->token = $zohoAccessToken->token->getToken();
                }
                if ($this->tokenExpiry->lessThanOrEqualTo(now()->addSeconds(10))) {
                    $zohoAccessToken = $store->get('token');

                    $zohoAccessToken = app(Provider::class)->getAccessToken(
                        'refresh_token',
                        ['refresh_token' => $zohoAccessToken->refresh_token]
                    );
                    $store->put('token', $zohoAccessToken);
                    $this->tokenExpiry = Carbon::createFromTimestamp($zohoAccessToken->token->getExpires());
                    $this->token = $zohoAccessToken->token->getToken();
                }

                return $this->token;
            });
    }
}
