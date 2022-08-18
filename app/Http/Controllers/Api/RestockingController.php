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

        return JsonResource::collection($this->getPaginatedResult($query->queryBuilder()));
    }
}
