<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserConfiguration extends Model
{
    protected $fillable = [
        'config'
    ];

    protected $casts = [
        'config' => 'array'
    ];
}
