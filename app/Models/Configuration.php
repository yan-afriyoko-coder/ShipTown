<?php

namespace App\Models;

use App\BaseModel;

/**
 * @property string $business_name
 * @property boolean $disable_2fa
 *
 */
class Configuration extends BaseModel
{
    protected $fillable = [
        'business_name',
        'disable_2fa'
    ];
}
