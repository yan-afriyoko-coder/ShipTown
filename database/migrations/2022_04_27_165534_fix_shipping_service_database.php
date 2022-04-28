<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class FixShippingServiceDatabase extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        \App\Models\ShippingService::query()->whereIn('code', ['dpd_ireland', 'dpd_uk', 'anpost']);

        \App\User::query()->where(['address_label_template' => 'dpd_ireland'])
            ->update(['address_label_template' => 'dpd_irl_next_day']);

        \App\User::query()->where(['address_label_template' => 'anpost'])
            ->update(['address_label_template' => 'anpost_3day']);

        \App\User::query()->where(['address_label_template' => 'dpd_uk'])
            ->update(['address_label_template' => 'dpd_uk_next_day']);
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
