<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNewIndexesToModulesWebhooksPendingWebhooksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('modules_webhooks_pending_webhooks', function (Blueprint $table) {
            $table->index('published_at');
            $table->index('reserved_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('modules_webhooks_pending_webhooks', function (Blueprint $table) {
            //
        });
    }
}
