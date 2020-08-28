<?php

namespace App\Http\Controllers\Api;

use App\Models\Pick;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Spatie\QueryBuilder\QueryBuilder;

class PickController extends Controller
{
    public function index(Request $request)
    {
        $pick = QueryBuilder::for(Pick::class);

        return $pick->paginate()->appends($request->query());
    }
}
