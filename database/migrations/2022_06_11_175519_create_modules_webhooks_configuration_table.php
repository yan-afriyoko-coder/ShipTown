<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateModulesWebhooksConfigurationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('modules_webhooks_configuration', function (Blueprint $table) {
            $table->id();
            $table->string('topic_arn')->nullable();
            $table->timestamps();
        });
    }
}
