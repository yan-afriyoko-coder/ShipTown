<?php

use App\Models\InventoryMovement;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        InventoryMovement::query()
            ->whereNull('occurred_at')
            ->update([
                'occurred_at' => DB::raw('created_at'),
            ]);

        Schema::table('inventory_movements', function (Blueprint $table) {
            $table->dateTime('occurred_at')->nullable(false)->change();
        });
    }
};
