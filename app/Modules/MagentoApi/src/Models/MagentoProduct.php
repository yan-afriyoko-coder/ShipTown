<?php

namespace App\Modules\MagentoApi\src\Models;

use App\BaseModel;
use App\Models\Product;
use App\Models\ProductPrice;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use Makeable\LaravelFactory\HasFactory;

/**
 * @property MagentoConnection $magentoConnection
 * @property Product $product
 * @property boolean base_price_sync_required
 * @property boolean special_price_sync_required
 * @property double $magento_price
 * @property double $magento_sale_price
 * @property Carbon $magento_sale_price_start_date
 * @property Carbon $magento_sale_price_end_date
 *
 * @property Carbon $base_prices_fetched_at
 * @property array $base_prices_raw_import
 * @property Carbon $special_prices_fetched_at
 * @property array $special_prices_raw_import
 * @property ProductPrice $prices
 *
 */
class MagentoProduct extends BaseModel
{
    use HasFactory;

    protected $table = 'modules_magento2api_products';

    protected $fillable = [
        'connection_id',
        'base_price_sync_required',
        'special_price_sync_required',
        'product_price_id',
        'product_id',
        'exists_in_magento',
        'connection_id',
        'magento_price',
        'magento_sale_price',
        'base_prices_fetched_at',
        'base_prices_raw_import',
        'special_prices_fetched_at',
        'special_prices_raw_import',
    ];

    protected $casts = [
        'base_price_sync_required' => 'boolean',
        'special_price_sync_required' => 'boolean',
        'magento_sale_price_start_date' => 'datetime',
        'magento_sale_price_end_date' => 'datetime',
        'stock_items_fetched_at' => 'datetime',
        'base_prices_fetched_at' => 'datetime',
        'special_prices_fetched_at' => 'datetime',
        'stock_items_raw_import'    => 'array',
        'base_prices_raw_import'    => 'array',
        'special_prices_raw_import'    => 'array',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function magentoConnection(): BelongsTo
    {
        return $this->belongsTo(MagentoConnection::class, 'connection_id');
    }

    public function prices(): BelongsTo
    {
        return $this->belongsTo(ProductPrice::class, 'product_price_id');
    }
}
