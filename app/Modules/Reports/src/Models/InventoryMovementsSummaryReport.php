<?php

namespace App\Modules\Reports\src\Models;

use App\Models\Inventory;
use App\Models\InventoryMovement;
use App\Models\Product;
use App\Models\Warehouse;
use App\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\DB;

class InventoryMovementsSummaryReport extends Report
{
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->report_name = 'Inventory Movements Summary';

        $this->defaultSelect = implode(',', [
            'type',
            'warehouse_code',
            'count'
        ]);

        $this->baseQuery = InventoryMovement::query()
            ->leftJoin('inventory', 'inventory.id', '=', 'inventory_movements.inventory_id')
            ->groupByRaw('inventory_movements.type, inventory.warehouse_code');

        $this->fields = [
            'type' => DB::raw('IFNULL(inventory_movements.type, "")'),
            'warehouse_code' => DB::raw('IFNULL(inventory.warehouse_code, "")'),
            'created_at' => 'inventory_movements.created_at',
            'count' => DB::raw('count(*)')
        ];

        $this->casts = [
            'type' => 'string',
            'warehouse_code' => 'string',
            'created_at' => 'datetime',
            'count' => 'integer'
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function inventory(): BelongsTo
    {
        return $this->belongsTo(Inventory::class);
    }

    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class);
    }
}
