<?php

namespace App\Modules\Rmsapi\src\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Barryvdh\LaravelIdeHelper\Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * App\Models\RmsapiProductImport.
 *
 * @property int         $id
 * @property int         $connection_id
 * @property string|null $batch_uuid
 * @property Carbon|null $reserved_at
 * @property Carbon|null $when_processed
 * @property int|null    $product_id
 * @property string|null $sku
 * @property array       $raw_import
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property RmsapiConnection $rmsapiConnection
 *
 * @method static Builder|RmsapiProductImport newModelQuery()
 * @method static Builder|RmsapiProductImport newQuery()
 * @method static Builder|RmsapiProductImport query()
 * @method static Builder|RmsapiProductImport whereBatchUuid($value)
 * @method static Builder|RmsapiProductImport whereConnectionId($value)
 * @method static Builder|RmsapiProductImport whereCreatedAt($value)
 * @method static Builder|RmsapiProductImport whereId($value)
 * @method static Builder|RmsapiProductImport whereProductId($value)
 * @method static Builder|RmsapiProductImport whereRawImport($value)
 * @method static Builder|RmsapiProductImport whereSku($value)
 * @method static Builder|RmsapiProductImport whereUpdatedAt($value)
 * @method static Builder|RmsapiProductImport whereWhenProcessed($value)
 * @mixin Eloquent
 */
class RmsapiProductImport extends Model
{
    use HasFactory;

    protected $table = 'modules_rmsapi_products_imports';

    protected $fillable = [
        'connection_id',
        'warehouse_code',
        'warehouse_id',
        'batch_uuid',
        'reserved_at',
        'when_processed',
        'product_id',
        'sku',
        'raw_import',
    ];

    protected $casts = [
        'raw_import' => 'array',
    ];

    // we use attributes to set default values
    // we won't use database default values
    // as this is then not populated
    // correctly to events
    protected $attributes = [
        'raw_import' => '{}',
    ];

    public function rmsapiConnection(): BelongsTo
    {
        return $this->belongsTo(RmsapiConnection::class, 'connection_id', 'id');
    }
}
