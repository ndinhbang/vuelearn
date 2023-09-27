<?php

namespace App\Src\Passport;

use Carbon\Carbon;
use Laravel\Passport\Passport;

class TokenRepository extends \Laravel\Passport\TokenRepository
{
    /**
     * {@inheritdoc}
     */
    public function find($id)
    {
        return Passport::token()
            ->where('id', $id)
            ->when(config('query-cache'), fn ($query) => $query->cache())
            ->first();
    }

    /**
     * {@inheritdoc}
     */
    public function findForUser($id, $userId)
    {
        return Passport::token()
            ->where('id', $id)
            ->where('user_id', $userId)
            ->when(config('query-cache'), fn ($query) => $query->cache())
            ->first();
    }

    /**
     * {@inheritdoc}
     */
    public function forUser($userId)
    {
        return Passport::token()
            ->where('user_id', $userId)
            ->when(config('query-cache'), fn ($query) => $query->cache())
            ->get();
    }

    /**
     * {@inheritdoc}
     */
    public function getValidToken($user, $client)
    {
        return $client->tokens()
            ->where('user_id', $user->getAuthIdentifier())
            ->where('revoked', 0)
            ->where('expires_at', '>', Carbon::now())
            ->when(config('query-cache'), fn ($query) => $query->cache())
            ->first();
    }

    /**
     * {@inheritdoc}
     */
    public function findValidToken($user, $client)
    {
        return $client->tokens()
            ->where('user_id', $user->getAuthIdentifier())
            ->where('revoked', 0)
            ->where('expires_at', '>', Carbon::now())
            ->latest('expires_at')
            ->when(config('query-cache'), fn ($query) => $query->cache())
            ->first();
    }

}
