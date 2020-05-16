<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;

class ConfigurationRmsApi extends Model
{
    protected $fillable = [
        'location_id', 'url', 'username', 'password'
    ];

    public function setPasswordAttribute($password)
    {
        $this->attributes['password'] = Crypt::encryptString($password);
    }
}
