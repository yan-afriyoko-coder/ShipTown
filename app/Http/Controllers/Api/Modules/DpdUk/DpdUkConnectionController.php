<?php

namespace App\Http\Controllers\Api\Modules\DpdUk;

use App\Http\Controllers\Controller;
use App\Http\Resources\DpdUkConnectionResource;
use App\Modules\DpdUk\src\Models\Connection as DpdUkConnection;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class DpdUkConnectionController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $query = DpdUkConnection::getSpatieQueryBuilder();

        return DpdUkConnectionResource::collection($this->getPaginatedResult($query));
    }
}
