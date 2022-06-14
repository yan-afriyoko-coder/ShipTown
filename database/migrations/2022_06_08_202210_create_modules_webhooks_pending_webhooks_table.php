<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateModulesWebhooksPendingWebhooksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('modules_webhooks_pending_webhooks', function (Blueprint $table) {
            $table->id();
            $table->string('model_class');
            $table->foreignId('model_id');
            $table->timestamp('reserved_at')->nullable();
            $table->timestamp('available_at')->nullable();
            $table->timestamps();
        });
    }
}
