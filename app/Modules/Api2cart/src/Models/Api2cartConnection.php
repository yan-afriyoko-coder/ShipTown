<?php

namespace App\Modules\Api2cart\src\Models;

use Barryvdh\LaravelIdeHelper\Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

/**
 * App\Modules\Api2cart\src\Models\Api2cartConnection.
 *
 * @property int $id
 * @property string $location_id
 * @property string $type
 * @property string $url
 * @property string $prefix
 * @property string|null $bridge_api_key
 * @property int|null $magento_store_id
 * @property string|null $magento_warehouse_id
 * @property string $last_synced_modified_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property int|null $inventory_location_id
 * @property int|null $pricing_location_id
 * @property-read Collection|Api2cartProductLink[] $productLinks
 * @property-read int|null $product_links_count
 * @property string|null inventory_warehouse_ids
 * @property int    pricing_source_warehouse_id
 * @property string|null inventory_source_warehouse_tag
 * @property int $inventory_source_warehouse_tag_id
 *
 * @method static Builder|Api2cartConnection newModelQuery()
 * @method static Builder|Api2cartConnection newQuery()
 * @method static Builder|Api2cartConnection query()
 * @method static Builder|Api2cartConnection whereBridgeApiKey($value)
 * @method static Builder|Api2cartConnection whereMagentoStoreId($value)
 * @method static Builder|Api2cartConnection whereMagentoWarehouseId($value)
 * @method static Builder|Api2cartConnection whereCreatedAt($value)
 * @method static Builder|Api2cartConnection whereId($value)
 * @method static Builder|Api2cartConnection whereLastSyncedModifiedAt($value)
 * @method static Builder|Api2cartConnection whereLocationId($value)
 * @method static Builder|Api2cartConnection wherePrefix($value)
 * @method static Builder|Api2cartConnection whereType($value)
 * @method static Builder|Api2cartConnection whereUpdatedAt($value)
 * @method static Builder|Api2cartConnection whereUrl($value)
 * @method static Builder|Api2cartConnection whereInventoryLocationId($value)
 * @method static Builder|Api2cartConnection wherePricingLocationId($value)
 *
 * @mixin Eloquent
 */
class Api2cartConnection extends Model
{
    use HasFactory;

    protected $table = 'modules_api2cart_connections';

    protected $fillable = [
        'prefix',
        'bridge_api_key',
        'inventory_source_warehouse_tag',
        'pricing_source_warehouse_id',
        'last_synced_modified_at',
        'url',
        'type',
    ];

    public function __construct(array $attributes = [])
    {
        $this->setRawAttributes([
            'last_synced_modified_at' => Carbon::now(),
        ], true);

        parent::__construct($attributes);
    }

    public function productLinks(): HasMany
    {
        return $this->hasMany(Api2cartProductLink::class);
    }
}
