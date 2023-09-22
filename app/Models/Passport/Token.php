<?php

namespace App\Models\Passport;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Laravel\Passport\Token as PassportToken;

class Token extends PassportToken
{
    use HasFactory;

    public function refreshToken(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(RefreshToken::class, 'access_token_id');
    }
}
