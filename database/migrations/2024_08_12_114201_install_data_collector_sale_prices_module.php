<?php

use Illuminate\Database\Migrations\Migration;
use App\Modules\DataCollectorSalePrices\src\DataCollectorSalePricesServiceProvider;

return new class extends Migration
{
    public function up(): void
    {
        DataCollectorSalePricesServiceProvider::installModule();
    }
};
