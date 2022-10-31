<?php

namespace App\Modules\MagentoApi\src\Models;

use App\BaseModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property MagentoProduct $magentoProduct
 * @property integer $magento_store_id
 * @property double $expected_price
 * @property string $sku
 */
class MagentoProductPricesComparisonView extends BaseModel
{
    protected $table = 'modules_magento2api_products_prices_comparison_view';

    public function magentoProduct(): BelongsTo
    {
        return $this->belongsTo(MagentoProduct::class, 'modules_magento2api_products_id');
    }
}
