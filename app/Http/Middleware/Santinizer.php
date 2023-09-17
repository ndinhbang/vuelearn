<?php

namespace App\Http\Middleware;

use GrahamCampbell\SecurityCore\Security;
use Illuminate\Foundation\Http\Middleware\TrimStrings as Middleware;

class Santinizer extends Middleware
{
    protected Security $santinizer;

    public function __construct(Security $santinizer)
    {
        $this->santinizer = $santinizer;
    }

    /**
     * The names of the attributes that should not be santinized.
     *
     * @var array<int, string>
     */
    protected $except = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    public function transform($key, $value)
    {
        $value = parent::transform($key, $value);

        return match (true) {
            is_bool($value), is_int($value), is_float($value), is_null($value) => $value,
            default => $this->santinizer->clean($value),
        };
    }
}
