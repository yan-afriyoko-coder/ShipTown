<?php

use App\Models\Warehouse;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        do {
            DB::statement('
                CREATE TEMPORARY TABLE tempTable AS
                SELECT id
                FROM inventory_movements
                WHERE `warehouse_code` IS NULL AND `warehouse_id` = 2
                LIMIT 10000;
            ');

            $recordsUpdated = DB::update('
                UPDATE inventory_movements
                INNER JOIN tempTable ON tempTable.id = inventory_movements.id
                LEFT JOIN warehouses ON warehouses.id = inventory_movements.warehouse_id
                SET inventory_movements.warehouse_code = warehouses.code;
            ');
        } while ($recordsUpdated > 0);
    }
};
