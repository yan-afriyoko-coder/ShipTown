<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Modules\Reports\src\Models\RestockingReport;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RestockingController extends Controller
{
    public function index(Request $request)
    {
        $query = new RestockingReport();

        $resource = $query->queryBuilder()
            ->simplePaginate(request()->get('per_page', 10))
            ->appends(request()->query());

        return JsonResource::collection($resource);
    }
}
