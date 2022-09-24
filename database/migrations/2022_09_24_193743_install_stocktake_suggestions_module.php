<?php

use App\Modules\StocktakeSuggestions\src\StocktakeSuggestionsServiceProvider;
use Illuminate\Database\Migrations\Migration;

class InstallStocktakeSuggestionsModule extends Migration
{
    public function up()
    {
        StocktakeSuggestionsServiceProvider::enableModule();
    }
}
