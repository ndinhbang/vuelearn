<?php

namespace App\Models\Passport;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Laravel\Passport\RefreshToken as PassportRefreshToken;
use Ndinhbang\QueryCache\Concerns\QueryCacheable;

class RefreshToken extends PassportRefreshToken
{
    use HasFactory, QueryCacheable;
}
