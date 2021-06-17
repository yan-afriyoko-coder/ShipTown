<?php

namespace App\Modules\AutoStatusPicking\src\Models;

use App\BaseModel;
use phpseclib\Math\BigInteger;

/**
 * Class ModuleAutoStatusPickings
 *
 * @package App\Modules\AutoStatusPicking\src\Models
 * @property BigInteger id
 * @property bool is_enabled
 * @property int $id
 * @property int $is_enabled
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|ModuleAutoStatusPickings newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ModuleAutoStatusPickings newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ModuleAutoStatusPickings query()
 * @method static \Illuminate\Database\Eloquent\Builder|ModuleAutoStatusPickings whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ModuleAutoStatusPickings whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ModuleAutoStatusPickings whereIsEnabled($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ModuleAutoStatusPickings whereUpdatedAt($value)
 * @mixin \Eloquent
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
