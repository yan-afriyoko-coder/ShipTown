<?php

use App\Modules\InventoryMovementsStatistics\src\InventoryMovementsStatisticsServiceProvider;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        InventoryMovementsStatisticsServiceProvider::enableModule();
    }
};
