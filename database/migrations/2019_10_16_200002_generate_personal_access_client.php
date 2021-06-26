<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Artisan;

class GeneratePersonalAccessClient extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
//        \Laravel\Passport\Client::query()->firstOr(function () {
//            Artisan::call('passport:client', ['--personal' => true, '--name' => config('app.name').' Personal Access Client']);
//            Artisan::call('passport:client', ['--password' => true, '--name' => config('app.name').' Password Grant Client']);
//        });
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
