<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ApiDataCollectorStoreRequest;
use App\Http\Requests\ApiDataCollectorUpdateRequest;
use App\Http\Resources\DataCollectionResource;
use App\Models\DataCollection;
use App\Modules\DataCollector\src\DataCollectorService;
use App\Modules\Reports\src\Models\DataCollectorListReport;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Resources\Json\JsonResource;

class DataCollectorController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $report = new DataCollectorListReport();

        return JsonResource::collection($report->toArray());
    }

    public function store(ApiDataCollectorStoreRequest $request): DataCollectionResource
    {
        $dc = DataCollection::create($request->validated());

        return DataCollectionResource::make($dc);
    }

    public function update(ApiDataCollectorUpdateRequest $request, DataCollection $dataCollector): DataCollectionResource
    {
        $dataCollector->update($request->validated());

        optional($request->get('action'), function ($action) use ($dataCollector) {
            DataCollectorService::runAction($dataCollector, $action);
        });

        return DataCollectionResource::make($dataCollector->refresh());
    }
}
