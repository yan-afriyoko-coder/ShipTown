<?php

namespace App\Modules\Reports\src\Models;

use App\Models\Inventory;
use App\Models\InventoryMovement;
use Illuminate\Database\Query\Builder;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\Filters\FiltersCallback;

class StocktakesReport extends Report
{
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->report_name = 'Stocktakes Report';

        $this->baseQuery = InventoryMovement::query()
            ->leftJoin('inventory', 'inventory_movements.product_id', '=', 'inventory.product_id')
            ->leftJoin('products as product', 'inventory_movements.product_id', '=', 'product.id')
            ->orderBy('inventory_movements.id', 'desc');

        $this->fields = [
            'date'                     => 'inventory.created_at',
            'warehouse_code'                     => 'inventory.warehouse_code',
            'product_sku'                        => 'product.sku',
            'product_name'                       => 'product.name',
            'quantity_delta'                     => 'inventory_movements.quantity_delta',
            'quantity_before'                    => 'inventory_movements.quantity_before',
            'quantity_after'                     => 'inventory_movements.quantity_after',
        ];

        $this->casts = [
            'restock_level'      => 'float',
            'reorder_point'      => 'float',
            'quantity_required'  => 'float',
            'quantity_available' => 'float',
            'quantity_incoming'  => 'float',
            'warehouse_quantity' => 'float',
        ];

        $this->addFilter(
            AllowedFilter::callback('has_tags', function ($query, $value) {
                $query->whereHas('product', function ($query) use ($value) {
                    $query->withAllTags($value);
                });
            })
        );
    }
}
