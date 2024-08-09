<?php

use Illuminate\Database\Migrations\Migration;
use App\Modules\DataCollectorGroupRecords\src\DataCollectorGroupRecordsServiceProvider;

return new class extends Migration
{
    public function up(): void
    {
        DataCollectorGroupRecordsServiceProvider::installModule();
    }
};
