<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Api2cartConnection extends Model
{
    //
    protected $fillable = [
        "prefix",
        "bridge_api_key",
        "last_synced_modified_at"
    ];

    protected $table = 'api2cart_connections';

}
