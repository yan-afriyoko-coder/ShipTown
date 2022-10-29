<?php

use App\Modules\MagentoApi\src\Models\MagentoConnection;
use App\Modules\MagentoApi\src\Models\MagentoProduct;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddConnectionIdColumnToModulesMagento2apiProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('modules_magento2api_products', function (Blueprint $table) {
            $table->foreignId('connection_id')->after('id');
        });

        $connection = MagentoConnection::first();

        if ($connection) {
            MagentoProduct::query()->update(['connection_id' => $connection->id]);
        }
    }
}
