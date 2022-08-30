<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\DataCollectorStoreRequest;
use App\Http\Resources\DataCollectionRecordResource;
use App\Models\DataCollectionRecord;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Auth;

class DataCollectorController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $query = DataCollectionRecord::getSpatieQueryBuilder();

        return DataCollectionRecordResource::collection($this->getPaginatedResult($query));
    }

    public function store(DataCollectorStoreRequest $request): AnonymousResourceCollection
    {
        $attributes = $request->validated();
        $attributes['user_id'] = Auth::id();

        /** @var DataCollectionRecord $dataCollectionRecord */
        $dataCollectionRecord = DataCollectionRecord::firstOrCreate([
            'user_id' => $attributes['user_id'],
            'product_id' => $attributes['product_id'],
        ]);

        $dataCollectionRecord->update([
            'quantity_collected' => $dataCollectionRecord->quantity_collected + $attributes['quantity_collected'],
        ]);


        return DataCollectionRecordResource::collection([$dataCollectionRecord]);
    }
}
