<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Widget extends Model
{
    protected $fillable = [
        'config',
        'name'
    ];

    protected $casts = [
        'config' => 'array'
    ];
}
