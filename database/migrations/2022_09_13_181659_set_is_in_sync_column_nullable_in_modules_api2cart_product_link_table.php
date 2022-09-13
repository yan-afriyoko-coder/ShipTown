<?php

use App\Modules\Api2cart\src\Models\Api2cartProductLink;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class SetIsInSyncColumnNullableInModulesApi2cartProductLinkTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('modules_api2cart_product_links', function (Blueprint $table) {
            $table->dropColumn('is_in_sync');
        });

        Schema::table('modules_api2cart_product_links', function (Blueprint $table) {
            $table->boolean('is_in_sync')->nullable()->index()->after('id');
        });
    }
}
