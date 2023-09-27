<?php

namespace App\Src\Passport;

use Laravel\Passport\Passport;
use RuntimeException;

class ClientRepository extends \Laravel\Passport\ClientRepository
{
    /**
     * {@inheritdoc}
     */
    public function find($id)
    {
        $client = Passport::client();

        return $client->where($client->getKeyName(), $id)
            ->when(config('query-cache'), fn ($query) => $query->cache())
            ->first();
    }

    /**
     * {@inheritdoc}
     */
    public function findForUser($clientId, $userId)
    {
        $client = Passport::client();

        return $client
            ->where($client->getKeyName(), $clientId)
            ->where('user_id', $userId)
            ->when(config('query-cache'), fn ($query) => $query->cache())
            ->first();
    }

    /**
     * {@inheritdoc}
     */
    public function forUser($userId)
    {
        return Passport::client()
            ->where('user_id', $userId)
            ->orderBy('name', 'asc')
            ->when(config('query-cache'), fn ($query) => $query->cache())
            ->get();
    }

    /**
     * {@inheritdoc}
     */
    public function personalAccessClient()
    {
        if ($this->personalAccessClientId) {
            return $this->find($this->personalAccessClientId);
        }

        $client = Passport::personalAccessClient();

        if (! $client->when(config('query-cache'), fn ($query) => $query->cache())->exists()) {
            throw new RuntimeException('Personal access client not found. Please create one.');
        }

        return $client->orderBy($client->getKeyName(), 'desc')
            ->when(config('query-cache'), fn ($query) => $query->cache())
            ->first()->client;
    }
}
