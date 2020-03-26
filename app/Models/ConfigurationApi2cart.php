<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ConfigurationApi2cart extends Model
{
    //
    protected $fillable = [
        "bridge_api_key",
        "last_synced_modified_at"
    ];

    protected $table = 'configuration_api2cart';

}
