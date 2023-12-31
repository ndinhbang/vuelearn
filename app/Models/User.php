<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use Ndinhbang\QueryCache\Concerns\QueryCacheable;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, QueryCacheable;

    protected $table = 'users';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_active' => 'boolean',
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    /**
     * Scope a query to only include popular users.
     */
    public function scopeActive(Builder $query): void
    {
        $query->where('is_active', true);
    }

    /**
     * @param $identifier
     * @return mixed
     */
    public function findForPassport($identifier): mixed
    {
        return $this->when(
            is_numeric($identifier),
            fn ($query) => $query->where('phone', $identifier),
            fn ($query) => $query->where('email', $identifier),
        )
            ->active()
            ->when(config('query-cache'), fn ($query) => $query->cache())
            ->first();
    }


}
