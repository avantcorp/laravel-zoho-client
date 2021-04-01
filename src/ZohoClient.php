<?php

namespace Avant\ZohoClient;

use Avant\ZohoClient\OAuth2\Provider;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

abstract class ZohoClient
{
    /** @var int|string */
    private $user;

    /** @var Carbon */
    private $tokenExpiry;

    /** @var string */
    private $token;

    public function __construct($user)
    {
        $this->user = $user instanceof Model ? $user->getKey() : $user;

        if (method_exists($this, 'boot')) {
            app()->call([$this, 'boot']);
        }

        collect(class_uses($this))
            ->each(function (string $trait) {
                $method = 'boot'.class_basename($trait);
                if (method_exists($this, $method)) {
                    app()->call([$this, $method]);
                }
            });
    }

    protected function getBaseUrl(): string
    {
        return $this->baseUrl;
    }

    protected function request()
    {
        return Http::baseUrl($this->getBaseUrl())
            ->asJson()
            ->withToken($this->getToken(), 'Zoho-oauthtoken');
    }

    private function getToken()
    {
        return Cache::lock("zoho-client.refresh_token.{$this->user}")
            ->block(10, function () {
                if (is_null($this->tokenExpiry) || is_null($this->token)) {
                    $zohoAccessToken = ZohoAccessToken::forUser($this->user);
                    $this->tokenExpiry = Carbon::createFromTimestamp($zohoAccessToken->token->getExpires());
                    $this->token = $zohoAccessToken->token->getToken();
                }
                if ($this->tokenExpiry->lessThanOrEqualTo(now()->addSeconds(10))) {
                    $zohoAccessToken = ZohoAccessToken::forUser($this->user);
                    $zohoAccessToken->update([
                        'token' => app(Provider::class)->getAccessToken(
                            'refresh_token',
                            ['refresh_token' => $zohoAccessToken->refresh_token]
                        ),
                    ]);
                    $this->tokenExpiry = Carbon::createFromTimestamp($zohoAccessToken->token->getExpires());
                    $this->token = $zohoAccessToken->token->getToken();
                }

                return $this->token;
            });
    }
}
