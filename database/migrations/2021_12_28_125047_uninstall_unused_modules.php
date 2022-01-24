<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UninstallUnusedModules extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
//        \App\Modules\AutoStatusPaid\src\AutoStatusPaidServiceProvider::uninstallModule();
        \App\Modules\AutoStatusAwaitingPayment\src\AutoStatusAwaitingPaymentServiceProvider::uninstallModule();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
