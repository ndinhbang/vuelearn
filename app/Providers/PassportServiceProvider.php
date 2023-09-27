<?php

namespace App\Providers;

use App\Src\Passport\Bridge\BearerTokenValidator;
use App\Src\Passport\ResponseTypes\BearerTokenResponse;
use Illuminate\Support\Facades\Auth;
use Laravel\Passport\Bridge\AccessTokenRepository;
use Laravel\Passport\Bridge\AuthCodeRepository;
use Laravel\Passport\Bridge\PersonalAccessGrant;
use Laravel\Passport\Bridge\RefreshTokenRepository;
use Laravel\Passport\Bridge\ScopeRepository;
use Laravel\Passport\ClientRepository;
use Laravel\Passport\Guards\TokenGuard;
use Laravel\Passport\Passport;
use Laravel\Passport\PassportUserProvider;
use Laravel\Passport\TokenRepository;
use League\OAuth2\Server\AuthorizationServer;
use League\OAuth2\Server\Grant\ClientCredentialsGrant;
use League\OAuth2\Server\Grant\RefreshTokenGrant;
use League\OAuth2\Server\ResourceServer;

class PassportServiceProvider extends \Laravel\Passport\PassportServiceProvider
{
    public function register()
    {
        $this->registerTokenRepository();
        $this->registerRefreshTokenRepository();
        $this->registerAuthCodeRepository();
        parent::register();
    }

    /**
     * Register the token repository.
     */
    protected function registerTokenRepository()
    {
        $this->app->bind(TokenRepository::class, function ($container) {
            return $container->make(\App\Src\Passport\TokenRepository::class);
        });
    }

    /**
     * Register the refresh token repository.
     */
    protected function registerRefreshTokenRepository()
    {
        $this->app->bind(\Laravel\Passport\RefreshTokenRepository::class, function ($container) {
            return $container->make(\App\Src\Passport\RefreshTokenRepository::class);
        });
    }

    /**
     * Register the auth code repository.
     */
    protected function registerAuthCodeRepository()
    {
        $this->app->bind(AuthCodeRepository::class, function ($container) {
            return $container->make(\App\Src\Passport\Bridge\AuthCodeRepository::class);
        });
    }

    /**
     * {@inheritdoc}
     */
    protected function registerClientRepository()
    {
        $this->app->singleton(ClientRepository::class, function ($container) {
            $config = $container->make('config')->get('passport.personal_access_client');
            return new \App\Src\Passport\ClientRepository($config['id'] ?? null, $config['secret'] ?? null);
        });
    }

    /**
     * {@inheritdoc}
     */
    public function makeAuthorizationServer()
    {
        return new \App\Src\Passport\AuthorizationServer(
            $this->app->make(\Laravel\Passport\Bridge\ClientRepository::class),
            $this->app->make(AccessTokenRepository::class),
            $this->app->make(ScopeRepository::class),
            $this->makeCryptKey('private'),
            app('encrypter')->getKey(),
            new BearerTokenResponse(),
        );
    }

    /**
     * {@inheritdoc}
     */
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
     * {@inheritdoc}
     */
    protected function registerResourceServer()
    {
        $this->app->singleton(ResourceServer::class, function ($container) {
            $accessTokenRepository = $container->make(AccessTokenRepository::class);
            $publicKey = $this->makeCryptKey('public');
            $tokenValidator = (new BearerTokenValidator($accessTokenRepository));
            $tokenValidator->setPublicKey($publicKey);
            return new ResourceServer($accessTokenRepository, $publicKey, $tokenValidator);
        });
    }

    /**
     * {@inheritdoc}
     */
    protected function makeRefreshTokenGrant()
    {
        $repository = $this->app->make(RefreshTokenRepository::class);

        return tap(new RefreshTokenGrant($repository), function ($grant) {
            $grant->setRefreshTokenTTL(Passport::refreshTokensExpireIn());
        });
    }
}
