<?php

namespace App\Models;

use App\Scopes\AuthenticatedUserScope;
use Illuminate\Database\Eloquent\Model;

class UserConfiguration extends Model
{
    protected $fillable = [
        'config'
    ];

    protected $casts = [
        'config' => 'array'
    ];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->user_id = auth()->id();
    }

    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope(new AuthenticatedUserScope());
    }
}
