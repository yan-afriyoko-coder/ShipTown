<?php

use App\Models\InventoryMovement;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up()
    {
        // sale
        InventoryMovement::query()
            ->where('type', '!=', null)
            ->whereIn('description', [
                'rms_sale'
            ])
            ->update(['type' => 'sale']);

        // adjustment
        InventoryMovement::query()
            ->where('type', '!=', null)
            ->whereIn('description', [
                'stocktake',
                'rms_adjustment'
            ])
            ->update(['type' => 'adjustment']);

        // transfer
        InventoryMovement::query()
            ->where('type', '!=', null)
            ->whereIn('description', [
                'data collection transfer in',
                'data collection transfer out',
                'rms_inventory_movement'
            ])
            ->update(['type' => 'transfer']);
    }
};
