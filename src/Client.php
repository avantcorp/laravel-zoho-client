<?php

namespace Avant\Zoho;

use Avant\Zoho\OAuth2\Provider;
use Carbon\Carbon;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;
use League\OAuth2\Client\Token\AccessToken;
use Spatie\Valuestore\Valuestore;

abstract class Client
{
    protected string $baseUrl;
    private ?AccessToken $token = null;

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
            ->lock('zoho.token', 10)
            ->block(5, function () {
                $store = Valuestore::make(config('services.zoho.token_storage_path'));
                $this->token ??= new AccessToken($store->get('token'));
                if (Carbon::createFromTimestamp($this->token->getExpires())->lessThanOrEqualTo(now()->addSeconds(10))) {
                    $this->token = app(Provider::class)
                        ->getAccessToken('refresh_token', ['refresh_token' => $store->get('refresh_token')]);
                    $store->put('token', $this->token);
                }

                return $this->token->getToken();
            });
    }
}
