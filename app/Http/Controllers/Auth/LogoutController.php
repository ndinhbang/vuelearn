<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class LogoutController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        $token = $request->user()->token();
        $token->revoke();
        $token->refreshToken->revoke();

        $cookieConfig = config('passport.cookie');
        return response()
            ->noContent()
            ->withoutCookie($cookieConfig['fingerprint'])
            ->withoutCookie($cookieConfig['refresh_token']);
    }
}
