<?php

namespace App\Modules\AutoStatusPicking\src\Models;

use App\BaseModel;
use phpseclib\Math\BigInteger;

/**
 * Class ModuleAutoStatusPickings
 * @package App\Modules\AutoStatusPicking\src\Models
 * @property BigInteger id
 * @property bool is_enabled
 */
class ModuleAutoStatusPickings extends BaseModel
{
    protected $fillable = [
        'is_enabled'
    ];

    protected $attributes = [
        'is_enabled' => true
    ];
}
