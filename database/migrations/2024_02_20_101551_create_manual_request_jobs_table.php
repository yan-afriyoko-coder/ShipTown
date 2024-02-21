<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('manual_request_jobs', function (Blueprint $table) {
            $table->id();
            $table->string('job_name');
            $table->string('job_class');
            $table->timestamps();

            $table->unique('job_name');
            $table->unique('job_class');
        });
    }
};
