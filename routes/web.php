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

Route::middleware('auth')->group(function () {

    Route::redirect('/', 'dashboard');

    Route::view('/dashboard', 'dashboard')
        ->name('dashboard');

    Route::view('/settings', 'settings')
        ->name('settings');

    Route::view('/products', 'products')
        ->name('products');

    Route::view('/picklist', 'picklist')
        ->name('picklist');

    Route::view('/users', 'users')
        ->name('users')
        ->middleware('can:manage users');

    Route::get('run/maintenance', function () {
        // this route should be moved to api and invoked trough button in settings
        // it should be done by 01/09/2020

        \App\Jobs\Orders\RecalculateOrderProductLineCountJob::dispatch();

        return 'Maintenance jobs dispatched';
    });

    // below everything is hidden from top navigation menu
    // but still available as direct link
    Route::view('/missing', 'missing')
        ->name('missing');

});

// Routes to allow invite other emails
Route::get('invites/{token}', 'InvitesController@accept')
    ->name('accept');

Route::post('invites/{token}', 'InvitesController@process');


// you can register only first user
// then he should invite others
try {
    Auth::routes(['register' => ! User::query()->exists()]);
} catch (\Exception $exception)
{
    Auth::routes(['register' => false]);
};







