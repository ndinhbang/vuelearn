<?php

namespace App\Models\Passport;

use Laravel\Passport\Client as PassportClient;
use Ndinhbang\QueryCache\Concerns\QueryCacheable;

class Client extends PassportClient
{
    use QueryCacheable;
    /**
     * Determine if the client should skip the authorization prompt.
     *
     * @return bool
     */
    public function skipsAuthorization(): bool
    {
        return $this->firstParty();
    }
}
