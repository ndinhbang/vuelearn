<?php

namespace App\Src;

use Illuminate\Auth\EloquentUserProvider;

class CacheableEloquentUserProvider extends EloquentUserProvider
{
    /**
     * {@inheritDoc}
     */
    protected function newModelQuery($model = null)
    {
        return parent::newModelQuery($model)
            ->when(config('query-cache'), fn ($query) => $query->cache());
    }
}