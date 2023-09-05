<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('modules_webhooks_pending_webhooks', function (Blueprint $table) {
            $table->string('sns_message_id')->nullable()->after('message');
        });
    }
};
