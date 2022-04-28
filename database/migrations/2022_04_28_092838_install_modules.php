<?php

use App\Modules\BaseModuleServiceProvider;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class InstallModules extends Migration
{
    private array $modules = [
        // misc modules
        App\Modules\Maintenance\src\EventServiceProviderBase::class,
        App\Modules\StockControl\src\StockControlServiceProvider::class,
        App\Modules\OrderTotals\src\OrderTotalsServiceProvider::class,
        App\Modules\OrderStatus\src\OrderStatusServiceProvider::class,
        App\Modules\FireActiveOrderCheckEvent\src\ActiveOrderCheckEventServiceProvider::class,

        App\Modules\InventoryReservations\src\EventServiceProviderBase::class,
        App\Modules\Automations\src\AutomationsServiceProvider::class,

        // Automations modules
        // order MIGHT be important!
        App\Modules\ShipmentConfirmationEmail\src\ServiceProvider::class,
        App\Modules\AutoPilot\src\AutoPilotServiceProvider::class,
        App\Modules\AutoTags\src\EventServiceProviderBase::class,
        App\Modules\OversoldProductNotification\src\OversoldProductNotificationServiceProvider::class,

        // AutoStatus modules
        // order is important!
        App\Modules\AutoStatusPicking\src\AutoStatusPickingServiceProvider::class,

        // 3rd party integrations
        // order SHOULD not be important
        App\Modules\Webhooks\src\WebhooksServiceProviderBase::class,
        App\Modules\Api2cart\src\Api2cartServiceProvider::class,
        App\Modules\Rmsapi\src\RmsapiModuleServiceProvider::class,
        App\Modules\MagentoApi\src\EventServiceProviderBase::class,
        App\Modules\ScurriAnpost\src\ScurriServiceProvider::class,
        App\Modules\DpdUk\src\DpdUkServiceProvider::class,
        App\Modules\AddressLabel\src\AddressLabelServiceProvider::class,
        App\Modules\DpdIreland\src\DpdIrelandServiceProvider::class,
    ];

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        collect($this->modules)->each(function (string $module_class) {
                \App\Models\Module::firstOrCreate([
                    'service_provider_class' => $module_class
                ], [
                    'enabled' => $module_class::$autoEnable,
                ]);
        });
    }
}
