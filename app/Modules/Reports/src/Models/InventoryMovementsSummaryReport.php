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

        $this->defaultSelect = 'type,warehouse_code,count';

        $this->baseQuery = InventoryMovement::query()
            ->leftJoin('inventory', 'inventory.id', '=', 'inventory_movements.inventory_id')
            ->groupByRaw('inventory_movements.type, inventory.warehouse_code')
            ->orderByRaw('inventory_movements.type, inventory.warehouse_code');

        $this->fields = [
            'created_at'        => DB::raw('inventory_movements.created_at'),
            'type'              => DB::raw('IFNULL(inventory_movements.type, "")'),
            'warehouse_code'    => DB::raw('IFNULL(inventory.warehouse_code, "")'),
            'count'             => DB::raw('count(*)'),
        ];

        $this->casts = [
            'count' => 'integer',
            'created_at' => 'datetime',
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
