<?php

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class GeneratePersonalAccessClient extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        \Laravel\Passport\Client::query()->whereNotExists(function(){
            Artisan::call('passport:client', ['--personal' => true, '--name' => config('app.name').' Personal Access Client']);
            Artisan::call('passport:client', ['--password' => true, '--name' => config('app.name').' Password Grant Client']);
        });
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
