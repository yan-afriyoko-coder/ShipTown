<?php

use App\Modules\Rmsapi\src\Models\RmsapiConnection;
use App\Modules\Rmsapi\src\Models\RmsapiSaleImport;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCommentColumnToModulesRmsapiSalesImportTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        RmsapiSaleImport::query()->forceDelete();

        Schema::table('modules_rmsapi_sales_imports', function (Blueprint $table) {
            $table->string('sku')->nullable()->after('processed_at');
            $table->decimal('price', 20)->nullable()->after('sku');
            $table->decimal('quantity', 20)->nullable()->after('price');
            $table->timestamp('transaction_time')->nullable()->after('quantity');
            $table->string('transaction_number')->nullable()->after('transaction_time');
            $table->integer('transaction_entry_id')->nullable()->after('transaction_number');
            $table->string('comment')->nullable()->after('transaction_entry_id');
        });

        RmsapiSaleImport::query()->forceDelete();

        RmsapiConnection::query()->update(['sales_last_timestamp' => 0]);
    }
}
