<?php

use App\Models\OrderAddress;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeDefaultEmailAddressValue extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('order_addresses', function (Blueprint $table) {
            $table->string('email')->default('')->change();
        });

        OrderAddress::query()->where(['email' => '0'])
            ->toBase() // this will prevent touching timestamps
            ->update([
                'email' => '',
            ]);
    }
}
