<?php

namespace App\Http\Controllers\Auth;

use App\Exceptions\RequestLockedException;
use App\Http\Controllers\Controller;
use App\Src\Passport\AuthorizationServer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Laravel\Passport\Exceptions\OAuthServerException;
use Laravel\Passport\Http\Controllers\ConvertsPsrResponses;
use Laravel\Passport\TokenRepository;
use League\OAuth2\Server\CryptTrait;
use League\OAuth2\Server\Exception\OAuthServerException as LeagueException;
use Nyholm\Psr7\Response as Psr7Response;
use Psr\Http\Message\ServerRequestInterface;

class RefreshController extends Controller
{
    use ConvertsPsrResponses, CryptTrait;

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
    public function __invoke(Request $request)
    {
        $refreshToken = $request->cookie(config('passport.cookie.refresh_token'));
        // Automic lock
        $lock = Cache::lock("lock:refresh:{$refreshToken}");
        // If you cant accquire the lock
        if (is_null($lock->get())) {
            throw new \App\Exceptions\RequestLockedException();
        }

        try {
            /**@var \App\Src\Passport\ResponseTypes\BearerTokenResponse $tokenResponse*/
            $tokenResponse = $this->server->getAccessTokenResponse(
                $this->tokenRequest->withParsedBody([
                    'grant_type' => 'refresh_token',
                    'refresh_token' => $refreshToken,
                    'client_id' => config('passport.password_grant_client.id'),
                    'client_secret' => config('passport.password_grant_client.secret'),
                    'scope' => '',
                ])
            );
            return $tokenResponse->toResponse();
        } catch (LeagueException $e) {
            throw new OAuthServerException($e, $this->convertResponse($e->generateHttpResponse(new Psr7Response)));
        } finally {
            $lock->release();
        }

    }

}
