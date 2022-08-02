<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPublishedAtColumnToModulesWebhooksPendingWebhooks extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('modules_webhooks_pending_webhooks', function (Blueprint $table) {
            $table->timestamp('published_at')->nullable()->after('available_at');
        });
    }
}
