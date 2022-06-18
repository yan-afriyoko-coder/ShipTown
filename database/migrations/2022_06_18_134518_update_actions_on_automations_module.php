<?php

use App\Modules\Automations\src\Models\Action;
use App\Modules\Automations\src\Models\Condition;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateActionsOnAutomationsModule extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Action::query()
            ->where(['action_class' => 'App\Modules\Automations\src\Actions\PushToBoxTopOrderAction'])
            ->update(['action_class' => 'App\Modules\Automations\src\Actions\Order\PushToBoxTopOrderAction']);

        Action::query()
            ->where(['action_class' => 'App\Modules\Automations\src\Actions\SendEmailToCustomerAction'])
            ->update(['action_class' => 'App\Modules\Automations\src\Actions\Order\SendEmailToCustomerAction']);

        Action::query()
            ->where(['action_class' => 'App\Modules\Automations\src\Actions\SendOrderEmailAction'])
            ->update(['action_class' => 'App\Modules\Automations\src\Actions\Order\SendOrderEmailAction']);

        Action::query()
            ->where(['action_class' => 'App\Modules\Automations\src\Actions\SetLabelTemplateAction'])
            ->update(['action_class' => 'App\Modules\Automations\src\Actions\Order\SetLabelTemplateAction']);

        Action::query()
            ->where(['action_class' => 'App\Modules\Automations\src\Actions\SplitBundleSkuAction'])
            ->update(['action_class' => 'App\Modules\Automations\src\Actions\Order\SplitBundleSkuAction']);

        Condition::query()
            ->where(['condition_class' => 'App\Modules\Automations\src\Conditions\IsFullyPickedCondition'])
            ->update(['condition_class' => 'App\Modules\Automations\src\Conditions\Order\IsFullyPickedCondition']);

        Condition::query()
            ->where(['condition_class' => 'App\Modules\Automations\src\Conditions\OrderNumberEqualsCondition'])
            ->update(['condition_class' => 'App\Modules\Automations\src\Conditions\Order\OrderNumberEqualsCondition']);

        Condition::query()
            ->where(['condition_class' => 'App\Modules\Automations\src\Conditions\ShippingMethodCodeInCondition'])
            ->update(['condition_class' => 'App\Modules\Automations\src\Conditions\Order\ShippingMethodCodeInCondition']);
    }
}
