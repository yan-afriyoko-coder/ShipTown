<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CompanyConfiguration extends Model
{
    protected $fillable = [
        "bridge_api_key"
    ];

    protected $table = "company_configuration";
}
