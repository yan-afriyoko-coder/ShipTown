<?php

namespace App\Http\Controllers\Api\Run;

use App\Http\Controllers\Controller;
use App\Modules\Api2cart\src\Models\Api2cartConnection;
use Carbon\Carbon;
use Illuminate\Http\Request;

/**
 * Class DailyJobsController
 * @package App\Http\Controllers\Api\Run
 */
class DailyJobsController extends Controller
{
    /**
     * @param Request $request
     */
    public function index(Request $request)
    {
        Api2cartConnection::all()
            ->each(function (Api2cartConnection $connection) {
                $connection->last_synced_modified_at = Carbon::createFromTimeString($connection->last_synced_modified_at)->subDay();
                $connection->save();
            });
    }
}
