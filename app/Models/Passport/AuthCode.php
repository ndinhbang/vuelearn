<?php

namespace App\Models\Passport;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Laravel\Passport\AuthCode as PassportAuthCode;
use Ndinhbang\QueryCache\Concerns\QueryCacheable;

class AuthCode extends PassportAuthCode
{
    use HasFactory, QueryCacheable;
}
