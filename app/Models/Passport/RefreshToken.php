<?php

namespace App\Models\Passport;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Laravel\Passport\RefreshToken as PassportRefreshToken;

class RefreshToken extends PassportRefreshToken
{
    use HasFactory;
}
