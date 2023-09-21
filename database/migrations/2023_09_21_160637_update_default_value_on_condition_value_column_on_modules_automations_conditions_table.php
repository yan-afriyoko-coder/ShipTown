<?php

use App\Modules\Automations\src\Models\Condition;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Condition::query()
            ->whereNull('condition_value')
            ->update(['condition_value' => '']);

        Schema::table('modules_automations_conditions', function (Blueprint $table) {
            $table->string('condition_value')->nullable(false)->default('')->change();
        });
    }
};
