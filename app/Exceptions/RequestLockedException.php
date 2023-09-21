<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RequestLockedException extends Exception
{
    public function report()
    {
        //
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response|\Illuminate\Http\JsonResponse
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function render( Request $request ): \Illuminate\Http\Response|\Illuminate\Http\JsonResponse
    {
        $message = 'Too many concurrent requests. Please try again later.';
        if ($request->expectsJson()) {
            return response()->json([
                'code'    => class_basename( $this ),
                'message' => $message
            ], Response::HTTP_LOCKED);
        }

        return response()->make($message, Response::HTTP_LOCKED);

    }
}
