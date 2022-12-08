<?php

namespace App\Modules\Reports\src\Models;

use App\Models\Inventory;
use App\Models\InventoryMovement;
use App\Models\Product;
use App\Models\Warehouse;
use App\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\QueryBuilder\AllowedFilter;

class InventoryMovementsReport extends Report
{
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->report_name = 'Stocktakes Report';

        $this->baseQuery = InventoryMovement::query()
            ->orderBy('inventory_movements.id', 'desc');

        $this->allowedIncludes = [
            'inventory',
            'product',
            'warehouse',
            'user',
        ];

        $this->fields = [
            'id'                => 'inventory_movements.id',
            'created_at'        => 'inventory_movements.created_at',
            'quantity_delta'    => 'inventory_movements.quantity_delta',
            'quantity_before'   => 'inventory_movements.quantity_before',
            'quantity_after'    => 'inventory_movements.quantity_after',
            'user_id'           => 'inventory_movements.user_id',
            'product_id'        => 'inventory_movements.product_id',
            'inventory_id'      => 'inventory_movements.inventory_id',
            'warehouse_id'      => 'inventory_movements.warehouse_id',
            'description'       => 'inventory_movements.description',
        ];

        $this->casts = [
            'created_at'        => 'datetime',
            'quantity_delta'    => 'float',
            'quantity_before'   => 'float',
            'quantity_after'    => 'float',
        ];

        $this->addFilter(
            AllowedFilter::callback('product_has_tags', function ($query, $value) {
                $query->whereHas('product', function ($query) use ($value) {
                    $query->hasTags($value);
                });
            })
        );

        $this->addFilter(
            AllowedFilter::callback('has_tags', function ($query, $value) {
                $query->whereHas('product', function ($query) use ($value) {
                    $query->withAllTags($value);
                });
            })
        );

        $this->addFilter(
            AllowedFilter::callback('search', function ($query, $value) {
                $query->whereHas('alias', function ($query) use ($value) {
                    $query->where(['alias' => $value]);
                });
            })
        );
    }

//    public function user(): BelongsTo
//    {
//        return $this->belongsTo(User::class);
//    }
//
//    public function product(): BelongsTo
//    {
//        return $this->belongsTo(Product::class);
//    }
//
//    public function inventory(): BelongsTo
//    {
//        return $this->belongsTo(Inventory::class);
//    }
//
//    public function warehouse(): BelongsTo
//    {
//        return $this->belongsTo(Warehouse::class);
//    }
}
