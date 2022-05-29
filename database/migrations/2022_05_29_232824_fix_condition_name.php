<?php

use App\Modules\Automations\src\Models\Condition;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class FixConditionName extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Condition::query()
            ->where(['condition_class' =>'App\Modules\OrderAutomations\src\Conditions\CanFulfillFromLocationCondition'])
            ->update(['condition_class' =>'App\Modules\Automations\src\Conditions\Order\CanFulfillFromLocationCondition']);
    }
}
