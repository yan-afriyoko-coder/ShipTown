<?php

namespace App\Modules\MagentoApi\src\Models;

use App\BaseModel;
use App\Models\Product;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property Product $product
 * @property boolean $is_in_stock
 * @property double $quantity
 * @property Carbon $stock_items_fetched_at
 */
class MagentoProductInventoryComparisonView extends BaseModel
{
    protected $table = 'modules_magento2api_products_inventory_comparison_view';

    protected $fillable = [
        'product_id',
        'connection_id',
        'is_in_stock',
        'quantity',
        'stock_items_fetched_at',
        'stock_items_raw_import',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
