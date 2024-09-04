<?php

namespace App\Modules\AutoStatusPicking\src\Models;

use App\BaseModel;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use phpseclib\Math\BigInteger;

/**
 * Class ModuleAutoStatusPickings.
 *
 * @property BigInteger id
 * @property bool is_enabled
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @method static Builder|ModuleAutoStatusPickings newModelQuery()
 * @method static Builder|ModuleAutoStatusPickings newQuery()
 * @method static Builder|ModuleAutoStatusPickings query()
 * @method static Builder|ModuleAutoStatusPickings whereCreatedAt($value)
 * @method static Builder|ModuleAutoStatusPickings whereId($value)
 * @method static Builder|ModuleAutoStatusPickings whereIsEnabled($value)
 * @method static Builder|ModuleAutoStatusPickings whereUpdatedAt($value)
 *
 * @mixin Eloquent
 *
 * @property int $id
 * @property int $is_enabled
 */
class ModuleAutoStatusPickings extends BaseModel
{
    protected $fillable = [
        'is_enabled',
    ];

    protected $attributes = [
        'is_enabled' => true,
    ];
}
