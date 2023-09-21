<?php

namespace App\Providers;

use App\Src\Passport\AuthorizationServer;
use App\Src\Passport\Bridge\BearerTokenValidator;
use Laravel\Passport\Bridge\PersonalAccessGrant;
use Laravel\Passport\Bridge\RefreshTokenRepository;
use Laravel\Passport\Passport;
use League\OAuth2\Server\Grant\ClientCredentialsGrant;
use League\OAuth2\Server\Grant\RefreshTokenGrant;
use League\OAuth2\Server\ResourceServer;

class PassportServiceProvider extends \Laravel\Passport\PassportServiceProvider
{
    /**
     * @return \App\Src\Passport\AuthorizationServer
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function makeAuthorizationServer()
    {
        return new AuthorizationServer(
            $this->app->make(\Laravel\Passport\Bridge\ClientRepository::class),
            $this->app->make(\Laravel\Passport\Bridge\AccessTokenRepository::class),
            $this->app->make(\Laravel\Passport\Bridge\ScopeRepository::class),
            $this->makeCryptKey('private'),
            app('encrypter')->getKey(),
            new \App\Src\Passport\ResponseTypes\BearerTokenResponse(),
        );
    }

    protected function registerAuthorizationServer()
    {
        $this->app->singleton(AuthorizationServer::class, function () {
            return tap($this->makeAuthorizationServer(), function ($server) {
                $server->setDefaultScope(Passport::$defaultScope);

                $server->enableGrantType(
                    $this->makeAuthCodeGrant(), Passport::tokensExpireIn()
                );

                $server->enableGrantType(
                    $this->makeRefreshTokenGrant(), Passport::tokensExpireIn()
                );

                $server->enableGrantType(
                    $this->makePasswordGrant(), Passport::tokensExpireIn()
                );

                $server->enableGrantType(
                    new PersonalAccessGrant, Passport::personalAccessTokensExpireIn()
                );

                $server->enableGrantType(
                    new ClientCredentialsGrant, Passport::tokensExpireIn()
                );
            });
        });
    }

    /**
     * Register the resource server.
     *
     * @return void
     */
    protected function registerResourceServer()
    {
        $this->app->singleton(ResourceServer::class, function ($container) {
            $accessTokenRepository = $container->make(\Laravel\Passport\Bridge\AccessTokenRepository::class);
            $publicKey = $this->makeCryptKey('public');
            $tokenValidator = (new BearerTokenValidator($accessTokenRepository));
            $tokenValidator->setPublicKey($publicKey);
            return new ResourceServer($accessTokenRepository, $publicKey, $tokenValidator);
        });
    }

    /**
     * Create and configure a Refresh Token grant instance.
     *
     * @return \League\OAuth2\Server\Grant\RefreshTokenGrant
     */
    protected function makeRefreshTokenGrant()
    {
        $repository = $this->app->make(RefreshTokenRepository::class);

        return tap(new RefreshTokenGrant($repository), function ($grant) {
            $grant->setRefreshTokenTTL(Passport::refreshTokensExpireIn());
        });
    }
}
