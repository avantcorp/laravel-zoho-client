<?php

namespace Avant\ZohoClient\Http\Controllers;

use Avant\ZohoClient\Http\Requests\AuthenticateRequest;
use Avant\ZohoClient\OAuth2\Provider;
use Avant\ZohoClient\ZohoAccessToken;
use Avant\ZohoClient\Http\Requests\CallbackAuthenticateRequest;

class OAuthController
{
    public function authenticate(Provider $provider, AuthenticateRequest $request)
    {
        $authorizationUrl = $provider->getAuthorizationUrl(['access_type' => 'offline', 'prompt' => 'consent']);

        session()->put($request->getStateKey(), $provider->getState());

        return redirect($authorizationUrl);
    }

    public function callback(Provider $provider, CallbackAuthenticateRequest $request)
    {
        session()->remove($request->getStateKey());

        $token = $provider->getAccessToken('authorization_code', ['code' => $request->code]);
        ZohoAccessToken::updateOrCreate([
            'user_id' => $request->user()->id,
        ], [
            'token'         => $token,
            'refresh_token' => $token->getRefreshToken(),
        ]);

        return redirect('/');
    }
}
