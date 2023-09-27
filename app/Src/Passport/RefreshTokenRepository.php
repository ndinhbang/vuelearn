<?php

namespace App\Src\Passport;

use Laravel\Passport\Passport;

class RefreshTokenRepository extends \Laravel\Passport\RefreshTokenRepository
{
    /**
     * {@inheritdoc}
     */
    public function find($id)
    {
        return Passport::refreshToken()
            ->where('id', $id)
            ->when(config('query-cache'), fn ($query) => $query->cache())
            ->first();
    }
}
