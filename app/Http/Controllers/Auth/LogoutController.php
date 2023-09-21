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

        return response()
            ->noContent()
            ->withoutCookie(config('passport.cookie.fingerprint'))
            ->withoutCookie(config('passport.cookie.refresh_token'));
    }
}
