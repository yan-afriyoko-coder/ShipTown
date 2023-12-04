<?php

use App\Models\Configuration;
use App\Models\MailTemplate;
use App\Models\NavigationMenu;
use App\Models\Warehouse;
use App\Modules\Automations\src\Actions\Order\SetStatusCodeAction;
use App\Modules\Automations\src\Conditions\Order\IsFullyPackedCondition;
use App\Modules\Automations\src\Conditions\Order\IsFullyPickedCondition;
use App\Modules\Automations\src\Conditions\Order\StatusCodeEqualsCondition;
use App\Modules\Automations\src\Models\Automation;
use App\Modules\AutoRestockLevels\src\AutoRestockLevelsServiceProvider;
use App\Modules\DataCollector\src\DataCollectorServiceProvider;
use App\Modules\InventoryMovementsStatistics\src\InventoryMovementsStatisticsServiceProvider;
use App\Modules\InventoryQuantityIncoming\src\InventoryQuantityIncomingServiceProvider;
use App\Modules\NonInventoryProductTag\src\NonInventoryProductTagServiceProvider;
use App\Modules\QueueMonitor\src\QueueMonitorServiceProvider;
use App\Modules\Slack\src\SlackServiceProvider;
use App\Modules\StocktakeSuggestions\src\StocktakeSuggestionsServiceProvider;
use App\Modules\Telescope\src\TelescopeModuleServiceProvider;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::transaction(function () {
            Artisan::call('app:install');
        });
    }
};
