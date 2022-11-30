<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Modules\Reports\src\Models\RestockingReport;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Cache;

class RestockingController extends Controller
{
    public function index(Request $request)
    {
        $query = new RestockingReport();

        $resource = $query->queryBuilder()
            ->simplePaginate(request()->get('per_page', 10))
            ->appends(request()->query());

        $anonymousResourceCollection = JsonResource::collection($resource);

        if ($request->has('cache_name')) {
            $keyName = implode('_', ['cached_restocking_report', 'user_id', auth()->id(), $request->get('cache_name')]);

            Cache::put($keyName, $anonymousResourceCollection, 300);
        }

        return $anonymousResourceCollection;
    }
}
