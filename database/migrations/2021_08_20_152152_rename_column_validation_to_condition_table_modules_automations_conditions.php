<?php

use App\Modules\Automations\src\Models\Condition;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RenameColumnValidationToConditionTableModulesAutomationsConditions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('modules_automations_conditions', function (Blueprint $table) {
            $table->renameColumn('validation_class', 'condition_class');
        });

        Condition::query()->get()->each(function (Condition $condition) {
            $condition->condition_class = Str::replaceFirst('Validators', 'Conditions', $condition->condition_class);
            $condition->condition_class = Str::replaceFirst('Validator', 'Condition', $condition->condition_class);
            $condition->save();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('modules_automations_conditions', function (Blueprint $table) {
            $table->renameColumn('condition_class', 'validation_class');
        });
    }
}
