<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        do {
            $recordsUpdated = DB::update('
            WITH tbl AS (
                SELECT id, inventory_id,
                       (
                           SELECT MAX(ID) as id
                           FROM inventory_movements as previous_inventory_movement
                           WHERE previous_inventory_movement.inventory_id = inventory_movements.inventory_id
                             AND previous_inventory_movement.id < inventory_movements.id
                       ) as previous_movement_id
                FROM inventory_movements
                WHERE inventory_movements.previous_movement_id IS NULL
                ORDER BY id DESC
                LIMIT 20
            )

            UPDATE inventory_movements
                INNER JOIN tbl ON
                        tbl.id = inventory_movements.id

            SET inventory_movements.previous_movement_id = tbl.previous_movement_id

            ');
        } while ($recordsUpdated > 0);
    }
};
