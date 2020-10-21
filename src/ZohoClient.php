<?php

namespace Avant\ZohoClient;

use Avant\ZohoClient\OAuth2\Provider;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Http;

abstract class ZohoClient
{
    protected $user;

    public function __construct($user)
    {
        $this->user = $user instanceof Model ? $user->getKey() : $user;
    }

    protected function getBaseUrl(): string
    {
        return $this->baseUrl;
    }

    protected function request()
    {
        return Http::baseUrl($this->getBaseUrl())
            ->asJson()
            ->withHeaders(['Authorization' => 'Zoho-oauthtoken '.$this->getToken()]);
    }

    private function getToken()
    {
        return once(function () {
            $zohoAccessToken = ZohoAccessToken::forUser($this->user);
            $zohoAccessToken->update([
                'data' => app(Provider::class)
                    ->getAccessToken('refresh_token', ['refresh_token' => $zohoAccessToken->refresh_token])
                    ->jsonSerialize(),
            ]);

            return $zohoAccessToken->token->getToken();
        });
    }
}
