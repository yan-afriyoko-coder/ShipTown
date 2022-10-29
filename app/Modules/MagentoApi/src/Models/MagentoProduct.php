<?php

namespace App\Modules\MagentoApi\src\Models;

use App\BaseModel;

class MagentoProduct extends BaseModel
{
    protected $table = 'modules_magento2api_products';

    /**
     * @var string[]
     */
    protected $fillable = [
        'stock_items_fetched_at',
        'stock_items_raw_import',
    ];

    protected $dates = [
        'stock_items_fetched_at',
    ];

    /**
     * @var string[]
     */
    protected $casts = [
        'stock_items_raw_import'    => 'array',
    ];
}
