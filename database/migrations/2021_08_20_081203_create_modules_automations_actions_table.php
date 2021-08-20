<?php

use App\Modules\Automations\src\Models\Action;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateModulesAutomationsActionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('modules_automations_executions')) {
            Schema::rename('modules_automations_executions', 'modules_automations_actions');

            Schema::table('modules_automations_actions', function (Blueprint $table) {
                $table->renameColumn('execution_class', 'action_class');
                $table->renameColumn('execution_value', 'action_value');

            });

            Action::query()->get()->each(function (Action $action) {
                $action->action_class = Str::replaceFirst('Executors', 'Actions', $action->action_class);
                $action->action_class = Str::replaceFirst('Executor', 'Action', $action->action_class);
                $action->save();
            });

            return;
        }

        if (Schema::hasTable('modules_automations_actions')) {
            return;
        }

        Schema::create('modules_automations_actions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('automation_id');
            $table->smallInteger('priority')->nullable(false)->default(0);
            $table->string('action_class')->nullable(false);
            $table->string('action_value')->nullable(false)->default('');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('modules_automations_actions');
    }
}
