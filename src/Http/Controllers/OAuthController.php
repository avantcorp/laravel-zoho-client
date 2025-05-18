<?php

namespace Avant\Zoho\Http\Controllers;

use Avant\Zoho\Http\Requests\CallbackAuthenticateRequest;
use Avant\Zoho\OAuth2\Provider;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Spatie\Valuestore\Valuestore;

class OAuthController
{
    public function authenticate(Provider $provider): RedirectResponse
    {
        if (Gate::has('zoho.authenticate')) {
            Gate::authorize('zoho.authenticate');
        }

        $authorizationUrl = $provider->getAuthorizationUrl(['access_type' => 'offline', 'prompt' => 'consent']);

        session()->put('zoho.state', $provider->getState());

        return redirect($authorizationUrl);
    }

    public function callback(Provider $provider, CallbackAuthenticateRequest $request): RedirectResponse
    {
        if (Gate::has('zoho.authenticate')) {
            Gate::authorize('zoho.authenticate');
        }

        $token = $provider->getAccessToken('authorization_code', $request->only('code'));

        Valuestore::make(config('services.zoho.token_storage_path'), [
            'token'         => $token,
            'refresh_token' => $token->getRefreshToken(),
        ]);

        return redirect(config('services.zoho.redirect_on_success'));
    }
}
