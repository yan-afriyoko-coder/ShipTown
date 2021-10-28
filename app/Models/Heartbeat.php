<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Heartbeat extends Model
{
    protected $fillable = ['code', 'error_message', 'expired_at'];

    public function scopeActive($query)
    {
        return $query->where('expired_at', ">", now());
    }
}
