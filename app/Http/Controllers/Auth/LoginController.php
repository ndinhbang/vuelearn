<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use League\OAuth2\Server\AuthorizationServer;
use Illuminate\Support\Facades\Cache;
use Laravel\Passport\Exceptions\OAuthServerException;
use Laravel\Passport\Http\Controllers\ConvertsPsrResponses;
use Laravel\Passport\TokenRepository;
use League\OAuth2\Server\Exception\OAuthServerException as LeagueException;
use Nyholm\Psr7\Response as Psr7Response;
use Psr\Http\Message\ServerRequestInterface;

class LoginController extends Controller
{
    use ConvertsPsrResponses;

    /**
     * @var \Psr\Http\Message\ServerRequestInterface
     */
    protected $tokenRequest;

    /**
     * The authorization server.
     *
     * @var \League\OAuth2\Server\AuthorizationServer
     */
    protected $server;

    /**
     * The token repository instance.
     *
     * @var \Laravel\Passport\TokenRepository
     */
    protected $tokens;

    /**
     * Create a new controller instance.
     *
     * @param \App\Src\Passport\AuthorizationServer $server
     * @param \Laravel\Passport\TokenRepository $tokens
     * @param \Psr\Http\Message\ServerRequestInterface $tokenRequest
     */
    public function __construct(AuthorizationServer $server, TokenRepository $tokens, ServerRequestInterface $tokenRequest)
    {
        $this->server = $server;
        $this->tokens = $tokens;
        $this->tokenRequest = $tokenRequest;
    }

    /**
     * Handle the incoming request.
     * @throws \Laravel\Passport\Exceptions\OAuthServerException
     * @throws \App\Exceptions\RequestLockedException
     */
    public function __invoke(LoginRequest $request)
    {
        // Automic lock
        $lock = Cache::lock("lock:login:{$request->username}");
        // If you cant accquire the lock
        if (is_null($lock->get())) {
            throw new \App\Exceptions\RequestLockedException();
        }

        try {
            $clientConfig = config('passport.password_grant_client');
            /**@var \App\Src\Passport\ResponseTypes\BearerTokenResponse $tokenResponse*/
            $tokenResponse = $this->server->getAccessTokenResponse(
                $this->tokenRequest->withParsedBody(
                    array_merge($request->validated(), [
                        'grant_type' => 'password',
                        'client_id' => $clientConfig['id'],
                        'client_secret' => $clientConfig['secret'],
                        'scope' => '*',
                    ]))
            );

            return $tokenResponse->toResponse();
        } catch (LeagueException $e) {
            throw new OAuthServerException($e, $this->convertResponse($e->generateHttpResponse(new Psr7Response)));
        } finally {
            $lock->release();
        }
    }
}
