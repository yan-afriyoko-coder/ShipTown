<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\BaseModel;
use App\Traits\LogsActivityTrait;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 *  DataCollection
 * @property int $id
 * @property int $warehouse_id
 * @property int $destination_warehouse_id
 * @property string $name
 * @property Carbon $deleted_at
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property DataCollectionRecord $records
 * @property Warehouse $warehouse
 * @property string $type
 * @property string $currently_running_task
 *
 */
class DataCollection extends BaseModel
{
    use HasFactory;

    use SoftDeletes;
    use LogsActivityTrait;

    protected static $logAttributes = [
        'deleted_at',
        'type',
        'currently_running_task'
    ];

    protected $fillable = [
        'type',
        'warehouse_id',
        'destination_warehouse_id',
        'name',
        'currently_running_task'
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
