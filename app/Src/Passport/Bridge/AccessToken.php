<?php

namespace App\Src\Passport\Bridge;

use App\Src\Passport\Concerns\TokenFingerprintTrait;
use DateTimeImmutable;
use Illuminate\Support\Str;
use Lcobucci\JWT\Token;
use League\OAuth2\Server\Entities\AccessTokenEntityInterface;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Entities\Traits\AccessTokenTrait;
use League\OAuth2\Server\Entities\Traits\EntityTrait;
use League\OAuth2\Server\Entities\Traits\TokenEntityTrait;

class AccessToken implements AccessTokenEntityInterface
{
    use AccessTokenTrait, EntityTrait, TokenEntityTrait, TokenFingerprintTrait;

    /**
     * Create a new token instance.
     *
     * @param  string  $userIdentifier
     * @param  array  $scopes
     * @param  \League\OAuth2\Server\Entities\ClientEntityInterface  $client
     * @return void
     */
    public function __construct($userIdentifier, array $scopes, ClientEntityInterface $client)
    {
        $this->setUserIdentifier($userIdentifier);

        foreach ($scopes as $scope) {
            $this->addScope($scope);
        }

        $this->setClient($client);
        $this->setFingerprint(Str::random(40));
    }

    /**
     * Generate a JWT from the access token
     *
     * @return Token
     */
    private function convertToJWT(): Token
    {
        $this->initJwtConfiguration();

        return $this->jwtConfiguration->builder()
            ->permittedFor($this->getClient()->getIdentifier())
            ->identifiedBy($this->getIdentifier())
            ->issuedAt(new DateTimeImmutable())
            ->canOnlyBeUsedAfter(new DateTimeImmutable())
            ->expiresAt($this->getExpiryDateTime())
            ->relatedTo((string) $this->getUserIdentifier())
            ->withClaim('fp', hash('sha256', $this->getFingerprint()))
            ->withClaim('scopes', $this->getScopes())
            ->getToken($this->jwtConfiguration->signer(), $this->jwtConfiguration->signingKey());
    }
}
