<?php

use App\Http\Controllers\Api;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Admin API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Routes for users with the admin role only]
Route::apiResource('settings/modules/printnode/printjobs', Api\Modules\Printnode\PrintJobController::class, ['as' => 'api.settings.module.printnode'])->only(['store']);
Route::apiResource('settings/modules/printnode/clients', Api\Modules\Printnode\ClientController::class, ['as' => 'api.settings.module.printnode'])->only(['index', 'store', 'destroy']);
Route::apiResource('settings/modules/rms_api/connections', Api\Modules\Rmsapi\RmsapiConnectionController::class, ['as' => 'api.settings.module.rmsapi'])->only(['index', 'store', 'destroy']);
Route::apiResource('settings/automations/config', Api\Modules\OrderAutomations\ConfigController::class, ['as' => 'api.settings.module.automations'])->only(['index']);
Route::apiResource('settings/automations', Api\Modules\OrderAutomations\AutomationController::class, ['as' => 'api.settings.module'])->only(['index', 'store', 'show', 'update', 'destroy']);
Route::apiResource('modules/autostatus/picking/configuration', Api\Modules\AutoStatus\ConfigurationController::class, ['as' => 'modules.autostatus.picking'])->only('index', 'store');
