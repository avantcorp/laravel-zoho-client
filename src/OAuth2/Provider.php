<?php

namespace Avant\ZohoClient\OAuth2;

use Avant\ZohoClient\ZohoClientServiceProvider;
use League\OAuth2\Client\Provider\AbstractProvider;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use League\OAuth2\Client\Token\AccessToken;
use Psr\Http\Message\ResponseInterface;

class Provider extends AbstractProvider
{
    public function __construct(string $clientId, string $clientSecret)
    {
        parent::__construct([
            'clientId'     => $clientId,
            'clientSecret' => $clientSecret,
            'redirectUri'  => route(ZohoClientServiceProvider::TAG . '.callback'),
        ]);
    }

    public function getBaseAuthorizationUrl(): string
    {
        return 'https://accounts.zoho.com/oauth/v2/auth';
    }

    public function getBaseAccessTokenUrl(array $params): string
    {
        return 'https://accounts.zoho.com/oauth/v2/token';
    }

    public function getResourceOwnerDetailsUrl(AccessToken $token): string
    {
        return 'https://accounts.zoho.com/';
    }

    protected function getDefaultScopes(): array
    {
        $scopes = config(ZohoClientServiceProvider::TAG.'services.zoho.scopes');
        if (is_string($scopes)) {
            $scopes = array_map('trim', explode(',', $scopes));
        }

        return $scopes;
    }

    protected function checkResponse(ResponseInterface $response, $data): void
    {
        if (isset($data['error'])) {
            throw new IdentityProviderException($data['error'], $response->getStatusCode(), $response);
        }
    }

    protected function createResourceOwner(array $response, AccessToken $token): ResourceOwner
    {
        return new ResourceOwner($response);
    }
}
