<?php

namespace App\Http\Controllers\Api;

use App\Models\Order;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Spatie\QueryBuilder\QueryBuilder;

class PacklistController extends Controller
{
    public function index(Request $request)
    {
        $query = QueryBuilder::for(Order::class)
            ->allowedIncludes('packlist')
            ->where('is_picked','=', true);

        return $query->paginate(1);
    }
}
