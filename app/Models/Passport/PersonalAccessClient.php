<?php

namespace App\Models\Passport;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Laravel\Passport\PersonalAccessClient as PassportPersonalAccessClient;
use Ndinhbang\QueryCache\Concerns\QueryCacheable;

class PersonalAccessClient extends PassportPersonalAccessClient
{
    use HasFactory, QueryCacheable;
}
