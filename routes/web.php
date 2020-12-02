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

    // Admin only routes
    Route::group(['middleware' => ['role:admin']], function () {
        Route::view('/users', 'users')->name('users');
    });

    // Reports routes
    Route::group(['prefix' => 'reports'], function () {
        Route::view('picks', 'reports/picks_report')->name('picks_report');
    });

    Route::view('/dashboard', 'dashboard')->name('dashboard');
    Route::view('/dashboard_test', 'dashboard_test')->name('dashboard_test');
    Route::view('/products', 'products')->name('products');
    Route::view('/picklist', 'picklist')->name('picklist');
    Route::view('/packlist', 'packlist')->name('packlist');
    Route::view('/orders', 'orders')->name('orders');
    Route::view('/settings', 'settings')->name('settings');

    Route::get('pdf/orders/{order_number}/{template}', 'PdfOrderController@show');
});
