<?php

use App\Models\OrderAddress;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrderAddressesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('order_addresses')) {
            return;
        }

        Schema::create('order_addresses', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->boolean('encrypted')->default(false);
            $table->string('company')->default('');
            $table->string('gender')->default('');
            $table->string('first_name')->default('');
            $table->string('last_name')->default('');
            $table->string('email')->default('');
            $table->string('address1')->default('');
            $table->string('address2')->default('');
            $table->string('postcode')->default('');
            $table->string('city')->default('');
            $table->string('state_code')->default('');
            $table->string('state_name')->default('');
            $table->string('country_code')->default('');
            $table->string('country_name')->default('');
            $table->string('phone')->default('');
            $table->string('fax')->default('');
            $table->string('website')->default('');
            $table->string('region')->default('');
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::table('order_addresses', function (Blueprint $table) {
            $table->string('first_name_encrypted')->nullable()->after('first_name');
            $table->string('last_name_encrypted')->nullable()->after('last_name');
            $table->string('phone_encrypted')->nullable()->after('phone');
            $table->string('email_encrypted')->nullable()->after('email');
        });

        Schema::table('order_addresses', function (Blueprint $table) {
            $table->string('email')->default('')->change();
        });

        OrderAddress::query()->where(['email' => '0'])
            ->toBase() // this will prevent touching timestamps
            ->update([
                'email' => '',
            ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('order_addresses');
    }
}
