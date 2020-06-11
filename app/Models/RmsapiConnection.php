<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;

class RmsapiConnection extends Model
{
    protected $fillable = [
        'location_id',
        'url',
        'username',
        'password',
        'products_last_timestamp'
    ];

    public function setPasswordAttribute($password)
    {
        $this->attributes['password'] = Crypt::encryptString($password);
    }
}
