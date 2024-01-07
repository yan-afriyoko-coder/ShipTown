<?php

use App\Models\Warehouse;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Warehouse::query()->get()->each(function (Warehouse $warehouse) {
            do {
                $recordsUpdated = DB::table('inventory_movements')
                    ->where('warehouse_id', $warehouse->id)
                    ->whereNull('warehouse_code')
                    ->limit(1000)
                    ->update(['warehouse_code' => $warehouse->code]);
            } while ($recordsUpdated > 0);
        });
    }
};
