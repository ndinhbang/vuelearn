<?php

namespace App\Models\Passport;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Laravel\Passport\Token as PassportToken;

class Token extends PassportToken
{
    use HasFactory;
}
