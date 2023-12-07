<?php

namespace App\Console\Commands;

use App;
use App\Modules\Api2cart\src\Models\Api2cartConnection;
use App\Modules\Api2cart\src\Models\Api2cartProductLink;
use App\Modules\Automations\src\Models\Action;
use App\Modules\Automations\src\Models\Automation;
use App\Modules\Automations\src\Models\Condition;
use App\Modules\DpdIreland\src\Models\DpdIreland;
use App\Modules\MagentoApi\src\Models\MagentoConnection;
use App\Modules\MagentoApi\src\Models\MagentoProduct;
use App\Modules\Maintenance\src\Jobs\CopyInventoryMovementsToNewTableJob;
use App\Modules\Rmsapi\src\Models\RmsapiConnection;
use App\Services\ModulesService;
use App\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Spatie\Activitylog\Models\Activity;
use Spatie\Tags\Tag;
use Symfony\Component\Console\Command\Command as CommandAlias;

class ClearDatabaseCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:clear-database';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clears the database except migrations table as this is intended only for testing purposes, please use with caution.';

    public function handle(): int
    {
        $this->call('down');

        $this->resetDatabase();

        Log::info('Database cleared.');

        DB::transaction(function () {
            Artisan::call('app:install');
        });

        Log::info('Application installed.');

        $this->call('up');

        return CommandAlias::SUCCESS;
    }

    public static function resetDatabase(): void
    {
        App\Modules\InventoryTotals\src\Models\Configuration::query()->forceDelete();
        App\Modules\InventoryTotals\src\Models\InventoryTotal::query()->forceDelete();
        App\Modules\InventoryTotals\src\Models\InventoryTotalByWarehouseTag::query()->forceDelete();
        App\Modules\InventoryMovements\src\Models\Configuration::query()->forceDelete();
        App\Modules\PrintNode\src\Models\Client::query()->forceDelete();
        App\Modules\InventoryReservations\src\Models\Configuration::query()->forceDelete();

        App\Models\InventoryMovementNew::query()->forceDelete();
        App\Models\NavigationMenu::query()->forceDelete();
        App\Models\Product::query()->forceDelete();
        App\Models\Inventory::query()->forceDelete();
        App\Models\ProductAlias::query()->forceDelete();
        App\Models\OrderProduct::query()->forceDelete();
        App\Models\Order::query()->forceDelete();
        App\Models\OrderStatus::query()->forceDelete();
        App\Models\Configuration::query()->forceDelete();
        App\Models\OrderProductTotal::query()->forceDelete();
        App\Models\InventoryMovement::query()->forceDelete();
        App\Models\Heartbeat::query()->forceDelete();
        App\Models\Module::query()->forceDelete();
        App\Models\DataCollection::query()->forceDelete();
        App\Models\DataCollectionRecord::query()->forceDelete();
        App\Models\InventoryMovement::query()->forceDelete();
        App\Models\Warehouse::query()->forceDelete();
        App\Models\Session::query()->forceDelete();
        App\Models\Configuration::query()->forceDelete();



        Tag::query()->forceDelete();
        Automation::query()->forceDelete();
        Condition::query()->forceDelete();
        Action::query()->forceDelete();
        Api2cartProductLink::query()->forceDelete();
        Api2cartConnection::query()->forceDelete();
        RmsapiConnection::query()->forceDelete();
        MagentoProduct::query()->forceDelete();
        MagentoConnection::query()->forceDelete();
        DpdIreland::query()->forceDelete();
        ModulesService::updateModulesTable();
        DB::table('modules_queue_monitor_jobs')->delete();
        User::query()->forceDelete();

        Activity::query()->forceDelete();


        App\Models\Configuration::query()->updateOrCreate([], ['disable_2fa' => true]);

        // now re-register all the roles and permissions (clears cache and reloads relations)
        app()->make(\Spatie\Permission\PermissionRegistrar::class)->registerPermissions();


        CopyInventoryMovementsToNewTableJob::dispatch();
        App\Modules\InventoryMovements\src\InventoryMovementsServiceProvider::enableModule();
        App\Modules\InventoryReservations\src\EventServiceProviderBase::enableModule();
    }

    private static function setVariable(string $key, string $value): void
    {
        $path = base_path('.env');

        if (file_exists($path)) {
            file_put_contents($path, str_replace(
                $key . '=' . env($key),
                $key . '=' . $value,
                file_get_contents($path)
            ));
        }
    }
}
