<?php

namespace App\Modules\Reports\src\Models;

use App\Models\Pick;

class PicksReport extends Report
{
    public string $report_name = 'Picks';

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->baseQuery = Pick::query()
            ->leftJoin('users as user', 'picks.user_id', '=', 'user.id')
            ->leftJoin('products as product', 'picks.product_id', '=', 'product.id')
            ->orderBy('picks.id', 'desc');

        $this->fields = [
            'warehouse_code' => 'picks.warehouse_code',
            'picked_at' => 'picks.created_at',
            'picker' => 'user.name',
            'product_sku' => 'product.sku',
            'product_name' => 'product.name',
            'picked' => 'picks.quantity_picked',
            'skipped' => 'picks.quantity_skipped_picking',
        ];

        $this->casts = [
            'warehouse_code' => 'string',
            'picked_at' => 'datetime',
            'picker' => 'string',
            'product_sku' => 'string',
            'product_name' => 'string',
            'picked' => 'float',
            'skipped' => 'float',
        ];
    }
}
