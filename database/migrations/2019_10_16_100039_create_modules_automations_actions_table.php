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
        if (Schema::hasTable('modules_automations_actions')) {
            return;
        }

        Schema::create('modules_automations_actions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('automation_id');
            $table->smallInteger('priority')->nullable(false)->default(0);
            $table->string('action_class')->nullable();
            $table->string('action_value')->nullable()->default('');
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
