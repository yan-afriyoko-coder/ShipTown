<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Api2CartConnection extends Model
{
    //
    protected $fillable = [
        "bridge_api_key",
        "last_synced_modified_at"
    ];

    protected $table = 'api2cart_connections';

}
