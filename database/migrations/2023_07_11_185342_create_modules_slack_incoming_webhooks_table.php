<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('modules_slack_incoming_webhooks', function (Blueprint $table) {
            $table->id();
            $table->string('webhook_url');
            $table->timestamps();
        });
    }
};
