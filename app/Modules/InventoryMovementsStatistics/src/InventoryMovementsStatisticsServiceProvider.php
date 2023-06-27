<?php

namespace App\Modules\InventoryMovementsStatistics\src;

use App\Events\EveryMinuteEvent;
use App\Events\HourlyEvent;
use App\Events\InventoryMovementCreatedEvent;
use App\Events\Product\ProductCreatedEvent;
use App\Events\SyncRequestedEvent;
use App\Modules\BaseModuleServiceProvider;
use Illuminate\Support\Facades\DB;

/**
 * Class EventServiceProviderBase.
 */
class InventoryMovementsStatisticsServiceProvider extends BaseModuleServiceProvider
{
    /**
     * @var string
     */
    public static string $module_name = '.CORE - Inventory Movements Statistics';

    /**
     * @var string
     */
    public static string $module_description = 'Statistics like "Quantity sold in last 7 days"';

    /**
     * @var bool
     */
    public static bool $autoEnable = true;

    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        SyncRequestedEvent::class => [
            Listeners\SyncRequestedEventListener::class,
        ],

        EveryMinuteEvent::class => [
            Listeners\EveryMinuteEventListener::class,
        ],

        InventoryMovementCreatedEvent::class => [
            Listeners\InventoryMovementCreatedEventListener::class,
        ],

        ProductCreatedEvent::class => [
            Listeners\ProductCreatedEventListener::class,
        ],
    ];

    public static function enabling(): bool
    {
        DB::table('modules_inventory_movements_statistics_last28days_sale_movements')->truncate();

        DB::statement('
            INSERT INTO modules_inventory_movements_statistics_last28days_sale_movements (
                inventory_movement_id, sold_at, inventory_id, quantity_sold
            )
            SELECT
                inventory_movements.id as inventory_movement_id,
                inventory_movements.created_at as sold_at,
                inventory_movements.inventory_id,
                inventory_movements.quantity_delta * -1 as quantity_sold
            FROM `inventory_movements`
            WHERE inventory_movements.type = "sale"
            AND inventory_movements.created_at >= DATE_SUB(NOW(), INTERVAL 28 DAY)
        ');

        return parent::enabling();
    }

    public static function disabling(): bool
    {
        return false;
    }
}
