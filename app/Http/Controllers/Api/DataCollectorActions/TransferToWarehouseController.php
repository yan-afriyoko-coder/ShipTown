<?php

namespace App\Http\Controllers\Api\DataCollectorActions;

use App\Http\Controllers\Controller;
use App\Modules\DataCollector\src\Jobs\TransferToJob;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TransferToWarehouseController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        TransferToJob::dispatch($request->get('data_collector_id'), $request->get('destination_warehouse_id'));

        return response()->json([
            'message' => 'Transfer started',
        ]);
    }
}
