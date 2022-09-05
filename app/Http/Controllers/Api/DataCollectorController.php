<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ApiDataCollectorStoreRequest;
use App\Http\Resources\DataCollectionResource;
use App\Models\DataCollection;
use App\Modules\Reports\src\Models\DataCollectorListReport;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Resources\Json\JsonResource;

class DataCollectorController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $report = new DataCollectorListReport();

        $resource = $report->queryBuilder()
            ->simplePaginate(request()->get('per_page', 10))
            ->appends(request()->query());

        return JsonResource::collection($resource);
    }

    public function store(ApiDataCollectorStoreRequest $request): DataCollectionResource
    {
        $dataCollection = DataCollection::create($request->validated());

        return DataCollectionResource::make($dataCollection);
    }
}
