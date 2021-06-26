<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

use App\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('manifest.json', 'ManifestController@index');

// you can register only first user then he should invite others
try {
    Auth::routes(['register' => !User::query()->exists()]);
} catch (\Exception $exception) {
    Auth::routes(['register' => false]);
}

// Routes to allow invite other emails
Route::get('invites/{token}', 'Api\Admin\UserInviteController@accept')->name('accept');
Route::post('invites/{token}', 'Api\Admin\UserInviteController@process');

// Routes for authenticated users only
Route::middleware('auth')->group(function () {
    Route::redirect('', 'dashboard');
    Route::view('dashboard', 'dashboard')->name('dashboard');
    Route::view('performance/dashboard', 'performance')->name('performance.dashboard');
    Route::view('products', 'products')->name('products');
    Route::view('picklist', 'picklist')->name('picklist');
    Route::view('orders', 'orders')->name('orders');

    Route::view('autopilot/packlist', 'autopilot/packlist')->name('autopilot.packlist');

    Route::resource('order/packsheet', 'Order\PacksheetController')->only(['show']);

    Route::view('reports/picks', 'reports/picks_report')->name('reports.picks');
    Route::get('reports/shipments', 'Reports\ShipmentController@index')->name('reports.shipments');
    Route::view('settings', 'settings')->name('settings');
    Route::view('settings/general', 'settings/general')->name('settings.general');
    Route::view('settings/module', 'settings/module')->name('settings.module');
    Route::view('settings/printnode', 'settings/printnode')->name('settings.printnode');
    Route::view('settings/rmsapi', 'settings/rmsapi')->name('settings.rmsapi');
    Route::view('settings/dpd-ireland', 'settings/dpd-ireland')->name('settings.dpd-ireland');
    Route::view('settings/api2cart', 'settings/api2cart')->name('settings.api2cart');
    Route::view('settings/api', 'settings/api')->name('settings.api');

    Route::get('pdf/orders/{order_number}/{template}', 'PdfOrderController@show');
    Route::get('orders/{order_number}/kick', 'OrderKickController@index');
    Route::get('products/24h/kick', 'Products24hKickController@index');
    Route::get('products/{sku}/kick', 'ProductKickController@index');
    Route::get('csv/ready_order_shipments', 'Csv\ReadyOrderShipmentController@index')->name('ready_order_shipments_as_csv');
    Route::get('csv/order_shipments', 'Csv\PartialOrderShipmentController@index')->name('partial_order_shipments_as_csv');
    Route::get('csv/products/picked', 'Csv\ProductsPickedInWarehouse@index')->name('warehouse_picks.csv');
    Route::get('csv/products/shipped', 'Csv\ProductsShippedFromWarehouseController@index')->name('warehouse_shipped.csv');

    // Admin only routes
    Route::middleware('role:admin')->group(function () {
        Route::group([], function () {

            // tools
//            Route::group(['prefix' => 'tools', 'namespace' => 'tools', 'as' => 'tools.'], function () {
            Route::get('admin/tools/log-viewer', '\Rap2hpoutre\LaravelLogViewer\LogViewerController@index');
            Route::prefix('admin/tools/queue-monitor')->group(function () {
                Route::queueMonitor();
            });
//            });
        });

        Route::view('users', 'users')->name('users');
        Route::get('sync-magento-api', function () {
            \App\Modules\MagentoApi\src\Jobs\SyncCheckFailedProductsJob::dispatch();
        });
    });
});
