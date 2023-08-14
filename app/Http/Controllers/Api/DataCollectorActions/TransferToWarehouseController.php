<?php

namespace App\Http\Controllers\Api\DataCollectorActions;

use App\Http\Controllers\Controller;
use App\Models\DataCollection;
use App\Models\DataCollectionTransferOut;
use App\Modules\DataCollector\src\Jobs\TransferToJob;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TransferToWarehouseController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $dataCollection = DataCollection::findOrFail($request->get('data_collector_id'));
        $dataCollection->update([
            'type' => DataCollectionTransferOut::class,
            'destination_warehouse_id' => $request->get('destination_warehouse_id'),
            'currently_running_task' => TransferToJob::class,
        ]);
        $dataCollection->delete();

        TransferToJob::dispatch($request->get('data_collector_id'));

        return response()->json([
            'message' => 'Transfer started',
        ]);
    }
}
