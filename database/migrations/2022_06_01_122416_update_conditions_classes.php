<?php

use App\Modules\Automations\src\Conditions\IsFullyPickedCondition as OldIsFullyPickedCondition;
use App\Modules\Automations\src\Conditions\Order\IsFullyPickedCondition;
use App\Modules\Automations\src\Conditions\OrderNumberEqualsCondition as OldOrderNumberEqualsCondition;
use App\Modules\Automations\src\Conditions\Order\OrderNumberEqualsCondition;
use App\Modules\Automations\src\Conditions\ShippingMethodCodeInCondition as OldShippingMethodCodeInCondition;
use App\Modules\Automations\src\Conditions\Order\ShippingMethodCodeInCondition;
use App\Modules\Automations\src\Models\Condition;
use Illuminate\Database\Migrations\Migration;

class UpdateConditionsClasses extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Condition::query()
            ->where(['condition_class' => OldIsFullyPickedCondition::class])
            ->update(['condition_class' => IsFullyPickedCondition::class]);

        Condition::query()
            ->where(['condition_class' => OldOrderNumberEqualsCondition::class])
            ->update(['condition_class' => OrderNumberEqualsCondition::class]);

        Condition::query()
            ->where(['condition_class' => OldShippingMethodCodeInCondition::class])
            ->update(['condition_class' => ShippingMethodCodeInCondition::class]);
    }
}
