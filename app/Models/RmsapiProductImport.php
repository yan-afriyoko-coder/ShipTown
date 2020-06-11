<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RmsapiProductImport extends Model
{
    protected $fillable = [
        'connection_id',
        'raw_import'
    ];

    protected $casts = [
        'raw_import' => 'array',
    ];

    // we use attributes to set default values
    // we wont use database default values
    // as this is then not populated
    // correctly to events
    protected $attributes = [
        'raw_import' => '{}',
    ];

}
