<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Packlist\StoreRequest;
use App\Http\Resources\PacklistResource;
use App\Models\Packlist;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class PacklistController extends Controller
{
    public function index(Request $request)
    {
        $inventory_location_id = $request->get('inventory_location_id', 100);

        $query = QueryBuilder::for(Packlist::class)
            ->allowedFilters([
                AllowedFilter::exact('order_id'),
                AllowedFilter::exact('is_picked'),
                AllowedFilter::exact('is_packed'),
                AllowedFilter::exact('order.order_number'),
            ])
            ->allowedIncludes([
                'product',
                'product.aliases',
                'user',
                'order'
            ])
            ->addInventorySource($inventory_location_id);

        return $query->paginate(999);
    }

    public function store(StoreRequest $request, Packlist $packlist)
    {
        $attributes = array_merge(
            $request->validated(),
            ['packer_user_id' => $request->user()->id]
        );

        $packlist->update($attributes);

        return new PacklistResource($packlist);
    }
}
