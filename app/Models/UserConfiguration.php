<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserConfiguration extends Model
{
    protected $fillable = [
        'jsonConfig'
    ];

    protected $casts = [
        'jsonConfig' => 'array'
    ];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->user_id = auth()->id();
    }
}
