<?php

namespace Avant\LaravelZohoClient;

use Avant\LaravelZohoClient\OAuth2\Provider;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;

abstract class ZohoClient
{
    protected $authToken;
    protected $baseUrl;

    public function __construct($user)
    {
        $user = $user instanceof Model ? $user->getKey() : $user;
        $this->authToken = cache()->remember(
            ZohoClientServiceProvider::TAG . '.token.' . Hash::make($user),
            config(ZohoClientServiceProvider::TAG . '.cache_timeout'),
            function () use ($user) {
                $zohoAccessToken = ZohoAccessToken::forUserId($user);
                $accessToken = app(Provider::class)->getAccessToken(
                    'refresh_token',
                    ['refresh_token' => $zohoAccessToken->refresh_token]
                );
                $zohoAccessToken->update(['data' => $accessToken->jsonSerialize()]);
                return $zohoAccessToken->token;
            }
        );
    }

    protected function getBaseUrl(): string
    {
        return $this->baseUrl;
    }

    protected function request()
    {
        return Http::baseUrl($this->baseUrl)
            ->asJson()
            ->withHeaders([
                'Authorization' => "Zoho-oauthtoken {$this->authToken}",
            ]);
    }
}
