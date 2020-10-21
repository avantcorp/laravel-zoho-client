<?php

namespace Avant\ZohoClient\OAuth2;

use League\OAuth2\Client\Provider\AbstractProvider;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use League\OAuth2\Client\Token\AccessToken;
use Psr\Http\Message\ResponseInterface;

class Provider extends AbstractProvider
{
    protected array $defaultScopes;

    public function __construct(string $clientId, string $clientSecret, string $redirectUri, array $defaultScopes)
    {
        $this->defaultScopes = $defaultScopes;

        parent::__construct([
            'clientId'     => $clientId,
            'clientSecret' => $clientSecret,
            'redirectUri'  => $redirectUri,
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
        return $this->defaultScopes;
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
