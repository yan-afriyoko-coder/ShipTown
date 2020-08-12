<?php

namespace App\Http\Controllers\Api;

use App\Models\Order;
use App\Models\Packlist;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class PacklistController extends Controller
{
    public function index(Request $request)
    {
        $inventory_location_id = $request->get('inventory_location_id', 100);

        $query = QueryBuilder::for(Packlist::class)
            ->allowedFilters([
                AllowedFilter::exact('order_id')
            ])
            ->allowedIncludes([
                'product',
                'user',
                'order'
            ])
            ->addInventorySource($inventory_location_id);

        return $query->paginate(999);
    }
}
