<?php

namespace Avant\LaravelZohoClient\Http\Controllers;

use Avant\LaravelZohoClient\Http\Requests\Request;
use Avant\LaravelZohoClient\OAuth2\Provider;
use Avant\LaravelZohoClient\ZohoAccessToken;
use Avant\LaravelZohoClient\Http\Requests\CallbackRequest;

class OAuthController
{
    public function authenticate(Provider $provider, Request $request)
    {
        $authorizationUrl = $provider->getAuthorizationUrl(['access_type' => 'offline']);
        session()->put($request->getStateKey(), $provider->getState());

        return redirect($authorizationUrl);
    }

    public function callback(Provider $provider, CallbackRequest $request)
    {
        session()->remove($request->getStateKey());
        $token = $provider->getAccessToken('authorization_code', ['code' => $request->code]);
        $zohoAccessToken = ZohoAccessToken::updateOrCreate(
            ['user_id' => $request->user()->id],
            ['data' => $token->jsonSerialize()]
        );
        if ($token->getRefreshToken()) {
            $zohoAccessToken->update(['refresh_token' => $token->getRefreshToken()]);
        }

        return response()->json($token->jsonSerialize());
    }
}
