<?php

use App\Models\StocktakeSuggestion;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        StocktakeSuggestion::query()->forceDelete();

        Schema::table('stocktake_suggestions', function (Blueprint $table) {
            $table->unique(['inventory_id', 'reason']);
        });
    }
};
