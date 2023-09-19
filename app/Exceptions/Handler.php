<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    /**
     * Report or log an exception.
     *
     * @param  \Throwable  $e
     * @return void
     *
     * @throws \Throwable
     */
    public function report(Throwable $e)
    {
        // Stop logging or reporting if this is an "access denied" (code 9) OAuthServerException.
        if (($e instanceof \Laravel\Passport\Exceptions\OAuthServerException && $e->getCode() === 9)
            || ($e instanceof \League\OAuth2\Server\Exception\OAuthServerException && $e->getCode() === 9)) {
            return;
        }
        parent::report($e);
    }
}
