<?php

namespace App\Src\Passport\Bridge;

use Laravel\Passport\Passport;

class AuthCodeRepository extends \Laravel\Passport\Bridge\AuthCodeRepository
{
    /**
     * {@inheritdoc}
     */
    public function isAuthCodeRevoked($codeId)
    {
        return Passport::authCode()
            ->where('id', $codeId)
            ->where('revoked', 1)
            ->when(config('query-cache'), fn ($query) => $query->cache())
            ->exists();
    }
}
