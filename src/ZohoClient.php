<?php

namespace Avant\ZohoClient;

use Avant\ZohoClient\OAuth2\Provider;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

abstract class ZohoClient
{
    private ZohoAccessToken $zohoAccessToken;

    /** @var int */
    private $user;

    public function __construct($user)
    {
        $this->user = $user instanceof Model ? $user->getKey() : $user;
        $this->zohoAccessToken = ZohoAccessToken::forUser($user);

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
            ->get(fn() => tap(
                ZohoAccessToken::forUser($this->user),
                function (ZohoAccessToken $zohoAccessToken) {
                    $tokenExpiry = Carbon::createFromTimestamp($zohoAccessToken->token->getExpires());
                    if ($tokenExpiry->lessThanOrEqualTo(now()->addSeconds(10))) {
                        $zohoAccessToken->update([
                            'token' => app(Provider::class)->getAccessToken(
                                'refresh_token',
                                ['refresh_token' => $zohoAccessToken->refresh_token]
                            ),
                        ]);
                    }
                })
                ->token
                ->getToken()
            );
    }
}
