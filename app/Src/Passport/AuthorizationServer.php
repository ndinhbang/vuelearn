<?php

namespace App\Src\Passport;

use League\OAuth2\Server\AuthorizationServer as BaseAuthorizationServer;
use League\OAuth2\Server\Exception\OAuthServerException;
use Psr\Http\Message\ServerRequestInterface;

class AuthorizationServer extends BaseAuthorizationServer
{
    /**
     * Return an access token response type instance.
     *
     * @see \League\OAuth2\Server\AuthorizationServer::respondToAccessTokenRequest
     * @param ServerRequestInterface $request
     * @return \League\OAuth2\Server\ResponseTypes\ResponseTypeInterface
     * @throws OAuthServerException
     */
    public function getAccessTokenResponse(ServerRequestInterface $request)
    {
        foreach ($this->enabledGrantTypes as $grantType) {
            if (!$grantType->canRespondToAccessTokenRequest($request)) {
                continue;
            }
            return $grantType->respondToAccessTokenRequest(
                $request,
                $this->getResponseType(),
                $this->grantTypeAccessTokenTTL[$grantType->getIdentifier()]
            );
        }

        throw OAuthServerException::unsupportedGrantType();
    }
}
