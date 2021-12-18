<?php

use App\Modules\Automations\src\Models\Action;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class SetDefaultStringOnActionValue2ColumnOnModulesAutomationsActionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Action::whereNull('action_value')->update(['action_value' => '']);

        Schema::table('modules_automations_actions', function (Blueprint $table) {
            $table->string('action_value')->nullable(false)->default('')->change();
            $table->string('action_class')->nullable(false)->default('')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
