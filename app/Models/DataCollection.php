<?php

namespace App\Models;

use App\BaseModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 *  DataCollection
 * @property int $id
 * @property int $warehouse_id
 * @property string $name
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property DataCollectionRecord $records
 * @property Warehouse $warehouse
 * @property string $type
 */
class DataCollection extends BaseModel
{
    use SoftDeletes;

    protected $fillable = [
        'type',
        'warehouse_id',
        'name',
    ];

    public function records(): HasMany
    {
        return $this->hasMany(DataCollectionRecord::class);
    }

    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class);
    }
}
