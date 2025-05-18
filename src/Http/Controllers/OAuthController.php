<?php

namespace Avant\ZohoClient\Http\Controllers;

use Avant\ZohoClient\Http\Requests\CallbackAuthenticateRequest;
use Avant\ZohoClient\OAuth2\Provider;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Spatie\Valuestore\Valuestore;

class OAuthController
{
    public function authenticate(Provider $provider): RedirectResponse
    {
        if (Gate::has('zoho-client.authenticate')) {
            Gate::authorize('zoho-client.authenticate');
        }

        $authorizationUrl = $provider->getAuthorizationUrl(['access_type' => 'offline', 'prompt' => 'consent']);

        session()->put('zoho-client.state', $provider->getState());

        return redirect($authorizationUrl);
    }

    public function callback(Provider $provider, CallbackAuthenticateRequest $request): RedirectResponse
    {
        if (Gate::has('zoho-client.authenticate')) {
            Gate::authorize('zoho-client.authenticate');
        }

        $token = $provider->getAccessToken('authorization_code', $request->only('code'));

        Valuestore::make(config('zoho_client.token_storage_path'), [
            'token'         => $token,
            'refresh_token' => $token->getRefreshToken(),
        ]);

        return redirect(config('zoho_client.redirect_on_success'));
    }
}
