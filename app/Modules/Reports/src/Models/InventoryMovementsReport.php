<?php

namespace App\Modules\Reports\src\Models;

use App\Models\InventoryMovement;
use App\Models\Warehouse;
use Spatie\QueryBuilder\AllowedFilter;

class InventoryMovementsReport extends Report
{
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->report_name = 'Stocktakes Report';

        $this->baseQuery = InventoryMovement::query();

        $this->allowedIncludes = [
            'inventory',
            'product',
            'warehouse',
            'user',
            'product.tags'
        ];

        $this->fields = [
            'id'                            => 'inventory_movements.id',
            'type'                          => 'inventory_movements.type',
            'sequence_number'               => 'inventory_movements.sequence_number',
            'occurred_at'                   => 'inventory_movements.occurred_at',
            'quantity_delta'                => 'inventory_movements.quantity_delta',
            'quantity_before'               => 'inventory_movements.quantity_before',
            'quantity_after'                => 'inventory_movements.quantity_after',
            'user_id'                       => 'inventory_movements.user_id',
            'product_id'                    => 'inventory_movements.product_id',
            'inventory_id'                  => 'inventory_movements.inventory_id',
            'warehouse_code'                => 'inventory_movements.warehouse_code',
            'warehouse_id'                  => 'inventory_movements.warehouse_id',
            'description'                   => 'inventory_movements.description',
            'updated_at'                    => 'inventory_movements.updated_at',
            'created_at'                    => 'inventory_movements.created_at',
            'custom_unique_reference_id'    => 'inventory_movements.custom_unique_reference_id',
        ];

        $this->casts = [
            'id'                => 'integer',
            'user_id'           => 'integer',
            'product_id'        => 'integer',
            'sequence_number'   => 'integer',
            'occurred_at'       => 'datetime',
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
}
