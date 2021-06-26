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
