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
    Auth::routes(['register' => ! User::query()->exists()]);
} catch (\Exception $exception) {
    Auth::routes(['register' => false]);
};

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

    Route::view('autopilot/packlist', 'bluelagoon')->name('autopilot.packlist');

    Route::view('order/packsheet', 'packsheet')->name('order.packsheet');
    Route::resource('order/packsheet', 'Order\PacksheetController')->only(['show']);

    Route::view('reports/picks', 'reports/picks_report')->name('reports.picks');
    Route::get('reports/shipments', 'Reports\ShipmentController@index')->name('reports.shipments');
    Route::view('settings', 'settings')->name('settings');

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
        Route::view('users', 'users')->name('users');
    });
});
