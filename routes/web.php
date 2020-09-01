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
use Illuminate\Support\Facades\Schema;

// you can register only first user then he should invite others
try {
    Auth::routes(['register' => ! User::query()->exists()]);
} catch (\Exception $exception) {
    Auth::routes(['register' => false]);
};

// Routes to allow invite other emails
Route::get('invites/{token}', 'InvitesController@accept')->name('accept');
Route::post('invites/{token}', 'InvitesController@process');

// Routes for authenticated users only
Route::middleware('auth')->group(function () {

    Route::redirect('/', 'dashboard');
    Route::view('/dashboard', 'dashboard')->name('dashboard');
    Route::view('/products', 'products')->name('products');

    Route::view('/picklist', 'picklist')->name('picklist');
    Route::view('/picklist/old', 'picklistOld')->name('picklistOld');

    Route::view('/packlist', 'packlist')->name('packlist');

    Route::view('/orders', 'orders')->name('orders');

    Route::get('pdf/orders/{order_number}/{template}', 'PdfOrderController@show');
    Route::view('/settings', 'settings')->name('settings');

    // below everything is hidden from top navigation menu but still available as direct link
    Route::view('/missing', 'missing')->name('missing');

    // this route should be moved to api and invoked trough button in settings, deadline01/09/2020
    Route::get('run/maintenance', function () {
        \App\Jobs\Orders\RecalculateOrderProductLineCountJob::dispatch();
        return 'Maintenance jobs dispatched';
    });

    Route::group(['middleware' => ['role:admin']], function () {
        Route::view('/users', 'users')->name('users');
    });
});
