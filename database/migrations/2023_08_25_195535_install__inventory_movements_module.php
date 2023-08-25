<?php

use App\Modules\InventoryMovements\src\InventoryMovementsServiceProvider;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        InventoryMovementsServiceProvider::installModule();
        InventoryMovementsServiceProvider::enableModule();
    }
};
