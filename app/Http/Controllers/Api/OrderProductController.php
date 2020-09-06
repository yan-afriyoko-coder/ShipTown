<?php

namespace App\Http\Controllers\Api;

use App\Models\OrderProduct;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class OrderProductController extends Controller
{
    public function index(Request $request)
    {
        $query = QueryBuilder::for(OrderProduct::class)
            ->allowedFilters([
                AllowedFilter::exact('order_id'),
            ]);

        $per_page = $request->get('per_page', 10);

        return $query->paginate($per_page)->appends($request->query());
    }
}
